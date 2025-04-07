<?php

require_once('config.php');
require_once('RecipeService.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

$service = new RecipeService($conn);
$response = $service->updateRecipe($data);

echo json_encode($response);

$conn->close();
