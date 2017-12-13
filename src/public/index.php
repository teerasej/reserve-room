<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require '../../vendor/autoload.php';

// $app = new \Slim\App;

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host'] = "localhost";
$config['db']['user'] = "root";
$config['db']['pass'] = "root";
$config['db']['dbname'] = "nextflow_reserve";
$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

// Add logger
$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

// Add PDO
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->get('/hi', function (Request $request, Response $response) {
    echo "Hi";
});

$app->get('/hello/{name}', function (Request $request, Response $response) {

    $this->logger->addInfo("Something interesting happened");
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->get('/room/reserved', function (Request $request, Response $response) {
    $params = $request->getQueryParams();

    // return $response->getBody()->write($params['sort']);
    return "test";
});

$app->get('/rooms', function (Request $request, Response $response) {
    $db = $this->db;

    // foreach($db->query("SELECT * FROM Room") as $row){
    //     $this->logger->addInfo($row['id'] . ' ' . $row['name']);
    // }

    $statement = $db->prepare("SELECT * FROM Room");
    $statement->execute();
    $results = $statement->fetchAll();
    echo json_encode($results);
});

$app->post('/room/reserve', function (Request $request, Response $response) {
    $this->logger->addInfo("posted reserve room");
    $data = $request->getParsedBody();
    $ticket_data = [];
    // $ticket_data['roomId'] = filter_var($data['roomId'], FILTER_SANITIZE_STRING);
    // $ticket_data['userId'] = filter_var($data['userId'], FILTER_SANITIZE_STRING);
    // $ticket_data['comment'] = filter_var($data['comment'], FILTER_SANITIZE_STRING);
    $ticket_data['roomId'] = $data['roomId'];
    $ticket_data['userId'] = $data['userId'];
    $ticket_data['comment'] = $data['comment'];
    $this->logger->addInfo(var_export($ticket_data, true));

    return "ok";
});

$app->post('/room/sync', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $this->logger->addInfo(var_export($data['room-reserve'], true));
});

$app->run();
