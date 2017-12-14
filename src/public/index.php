<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;



require '../../vendor/autoload.php';
require 'setting.php';

$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

require 'dependencies.php';
require 'middleware.php';

$app->get('/hi', function (Request $request, Response $response) {
    echo "Hi";
});

require 'room.php';
require 'authen.php';

// $app->get('/hello/{name}', function (Request $request, Response $response) {

//     $this->logger->addInfo("Something interesting happened");
//     $name = $request->getAttribute('name');
//     $response->getBody()->write("Hello, $name");

//     return $response;
// });



$app->run();
