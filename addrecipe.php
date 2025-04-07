<?php
require_once 'config.php';
require_once('listrecipe.php');

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$service = new RecipeService($conn);
$response = $service->addRecipe($data);

http_response_code($response['status']);
echo json_encode($response);

$conn->close();
