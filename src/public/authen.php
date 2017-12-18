<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use \Firebase\JWT\JWT;

$app->post('/signin', function (Request $request, Response $response) {



    // Get username and password
    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];

    // Check user existance in DB
    $db = $this->db;
    $statement = $db->prepare("SELECT * FROM User WHERE username=:username AND password=:password");
    $statement->execute(array(':username' => $username, ':password' => $password));
    $current_user = $statement->fetch();
    $db = null;


    if (!isset($current_user)) {
        echo json_encode("No user found");
    } else {
        $sql = "SELECT * FROM tokens
            WHERE user_id = :user_id AND date_expiration >" . time();
        $token_from_db = false;
        try {
            $db = $this->db;
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $current_user['id']);
            $stmt->execute();
            $token_from_db = $stmt->fetchObject();
            $db = null;
            if ($token_from_db) {
                echo json_encode([
                    "token" => $token_from_db->value,
                    "user_login" => $token_from_db->user_id,
                ]);
            }
        } catch (PDOException $e) {
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }

        // Existing user is logging in
        if (count($current_user) != 0 && !$token_from_db) {

            // Prepare data for token
            $key = $this->get('settings')['tokenSecretKey'];
            $payload = array(
                "iss" => "http://localhost:8888",
                "iat" => time(),
                "exp" => time() + (3600 * 24 * 15),
                "context" => [
                    "user" => [
                        "user_login" => $current_user['username'],
                        "user_id" => $current_user['id'],
                    ],
                ],
            );

            // Encoding token
            try {
                $jwt = JWT::encode($payload, $key);
            } catch (Exception $e) {
                echo json_encode($e);
            }

            // Create new token and return to client
            $sql = "INSERT INTO tokens (user_id, value, date_created, date_expiration)
                        VALUES (:user_id, :value, :date_created, :date_expiration)";
            try {
                $db = $this->db;
                $stmt = $db->prepare($sql);
                $stmt->bindParam("user_id", $current_user['id']);
                $stmt->bindParam("value", $jwt);
                $stmt->bindParam("date_created", $payload['iat']);
                $stmt->bindParam("date_expiration", $payload['exp']);
                $stmt->execute();
                $db = null;
                echo json_encode([
                    "token" => $jwt,
                    "user_login" => $current_user['id'],
                ]);
            } catch (PDOException $e) {
                echo '{"error":{"text":' . $e->getMessage() . '}}';
            }
        }
    }

});

$app->get('/restricted', function (Request $request, Response $response) {

    echo json_encode([
        "response" => "This is your secure resource !"
    ]);

    // $jwt = $request->getHeaders();
    // $key = $this->get('settings')['tokenSecretKey'];

    // $this->logger->addInfo($jwt['HTTP_AUTHORIZATION'][0]);

    // try {
    //     $decoded = JWT::decode($jwt['HTTP_AUTHORIZATION'][0], $key, array('HS256'));
    // } catch (UnexpectedValueException $e) {
    //     echo $e->getMessage();
    // }
    // if (isset($decoded)) {
    //     $sql = "SELECT * FROM tokens WHERE user_id = :user_id";
    //     try {
    //         $db = $this->db;
    //         $stmt = $db->prepare($sql);
    //         $stmt->bindParam("user_id", $decoded->context->user->user_id);
    //         $stmt->execute();
    //         $user_from_db = $stmt->fetchObject();
    //         $db = null;
    //         if (isset($user_from_db->user_id)) {
    //             echo json_encode([
    //                 "response" => "This is your secure resource !"
    //             ]);
    //         } else {
    //             echo json_encode([
    //                 "response" => "Sorry, you are not allowed"
    //             ]);
    //         }
    //     } catch (PDOException $e) {
    //         echo '{"error":{"text":' . $e->getMessage() . '}}';
    //     }
    // }
});

?>
