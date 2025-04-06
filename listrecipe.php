<?php

class RecipeService
{
    private $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function getAllRecipes()
    {
        $query = $this->conn->prepare('SELECT * FROM data');

        if ($query && $query->execute()) {
            $result = $query->get_result();

            if ($result->fetch_row() === null) 
            {
                return ["status" => 404, "message" => "No recipes found"];
            }
            $result->data_seek(0); // Reset pointer
            $recipes = $result->fetch_all(MYSQLI_ASSOC);
            return ["status" => 200, "data" => $recipes];
        }

        return ["status" => 500, "message" => "Query execution failed"];
    }
}
