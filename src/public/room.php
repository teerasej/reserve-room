<?php

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

?>