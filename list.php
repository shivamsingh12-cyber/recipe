<?php
require_once('config.php'); // Sets up $conn
require_once('listrecipe.php');

header("Content-Type: application/json");

$service = new RecipeService($conn);
$response = $service->getAllRecipes();
http_response_code($response['status']);
echo json_encode($response);

$conn->close();
