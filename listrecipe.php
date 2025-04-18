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

    public function addRecipe($data)
    {
        if (!isset($data['name'], $data['prep_time'], $data['difficulty'], $data['vegetarian'], $data['rate'])) {
            return ['status' => 400, 'message' => 'Invalid input'];
        }

        $stmt = $this->conn->prepare(
            'INSERT INTO data(name, prep_time, difficulty, vegetarian, rate) VALUES (?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            return ['status' => 500, 'message' => 'Preparation failed'];
        }

        $stmt->bind_param(
            'sssss',
            $data['name'],
            $data['prep_time'],
            $data['difficulty'],
            $data['vegetarian'],
            $data['rate']
        );

        if ($stmt->execute()) {
            return ['status' => 200, 'message' => 'Recipe added successfully'];
        }

        return ['status' => 500, 'message' => 'Query execution failed'];
    }
}
