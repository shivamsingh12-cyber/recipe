<?php
require_once('config.php');
require_once('RecipeService.php');

header("Content-Type: application/json");

if (!isset($_GET['rid'])) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Recipe ID is required"]);
    exit;
}

$recipeService = new RecipeService($conn);
$response = $recipeService->getRecipeById($_GET['rid']);
http_response_code($response['status']);
echo json_encode($response);
$conn->close();
