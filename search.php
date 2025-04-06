<?php
require_once('config.php'); // Contains DB credentials

header("Content-Type: application/json");



// Check if 'rid' is provided in the URL
$data=json_decode(file_get_contents("php://input"), true);

$recipeId = $data['sname'];



// Prepare SQL query
$name = "%{$recipeId}%";
$query = $conn->prepare('SELECT * FROM data WHERE name LIKE ?');
$query->bind_param('s', $name);

if ($query->execute())
{
    $result = $query->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => 404, "message" => "Recipe not found"]);
        exit;
    }
 
    $recipe= $result->fetch_all(MYSQLI_ASSOC);
    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $recipe]);
} else 
{
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Query execution failed"]);
}

$conn->close();
?>
