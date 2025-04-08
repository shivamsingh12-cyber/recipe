<?php

require_once('config.php');
require_once('RecipeService.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

$input = json_decode(file_get_contents("php://input"), true);

$service = new RecipeService($conn);
$response = $service->deleteRecipe($input);

echo json_encode($response);

$conn->close();
