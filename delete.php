<?php

require_once('config.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['unique_id'])) {
        $unique_id = $input['unique_id'];

        $query = $conn->prepare('DELETE FROM data WHERE unique_id=?');
        $query->bind_param('s', $unique_id);

        if ($query->execute()) {
            http_response_code(200);
            echo json_encode(["status" => 200, "message" => "Recipe deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => 500, "message" => "Query execution failed"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => 400, "message" => "unique_id is required"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => 405, "message" => "Only DELETE method allowed"]);
}

$conn->close();
