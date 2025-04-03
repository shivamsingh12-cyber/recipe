<?php
require_once('config.php');
header("Content-Type: application/json");
class ListAll extends DbCredentials{
    public function getRecipes(){
  
        $conn=new mysqli($this->hostName(),$this->userName(),$this->password(),$this->dbName());
        if ($conn->connect_error) {
            // http_response_code(500);
            echo json_encode(["status" => 500, "message" => "Database connection error"]);
            return;
        }

        $query = $conn->prepare('SELECT unique_id, name, prep_time, difficulty, vegetarian FROM data');
        $query->execute();
        $result = $query->get_result();

        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }

        echo json_encode([
            "status" => 200,
            "data" => $recipes
        ]);
    }
}

$api = new ListAll();
$api->getRecipes();


?>