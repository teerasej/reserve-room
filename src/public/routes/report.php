<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/report/month', function (Request $request, Response $response) {
    $db = $this->db;

    $statement = $db->prepare("SELECT * FROM Report");
    $statement->execute();
    $results = $statement->fetchAll();
    echo json_encode($results);
});

?>