<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/report/month', function (Request $request, Response $response) {
    $db = $this->db;

    // foreach($db->query("SELECT * FROM Room") as $row){
    //     $this->logger->addInfo($row['id'] . ' ' . $row['name']);
    // }

    $statement = $db->prepare("SELECT * FROM Room");
    $statement->execute();
    $results = $statement->fetchAll();
    echo json_encode($results);
});

?>