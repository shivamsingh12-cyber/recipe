<?php

require_once('config.php'); // Contains DB credentials

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: Post");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");





// Check if 'rid' is provided in the URL
$data=json_decode(file_get_contents("php://input"), true);
if (isset($data['unique_id'])) {


$name = $data['name'];
$prep_time = $data['prep_time'];
$difficulty = $data['difficulty'];  
$vegetarian = $data['vegetarian'];
$unique_id = $data['unique_id'];



// Prepare SQL query
$query = $conn->prepare('UPDATE data SET name=?, prep_time=?, difficulty=?, vegetarian=? WHERE unique_id=?');
$query->bind_param('sssss', $name, $prep_time, $difficulty, $vegetarian, $unique_id);

if ($query->execute()) 
{
    // $result = $query->get_result();

    http_response_code(200);
    echo json_encode(["status" => 200, "message" => "Recipe Updated successfully"]);
} else 
{
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => "Query execution failed"]);
}
}else {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "unique_id is required"]);
}
$conn->close();
?>
