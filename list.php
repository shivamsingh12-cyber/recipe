<?php
require_once('config.php'); // Contains DB credentials

header("Content-Type: application/json");

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Database connection error"]);
    exit;
}





// Prepare SQL query
$query = $conn->prepare('SELECT * FROM data');

if ($query->execute()) {
    $result = $query->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => 404, "message" => "Recipe not found"]);
        exit;
    }

    $recipe = $result->fetch_assoc();
    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $recipe]);
} else {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Query execution failed"]);
}

$conn->close();
?>
