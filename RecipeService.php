<?php

class RecipeService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addRecipe($data)
    {
        if (
            empty($data['name']) ||
            empty($data['prep_time']) ||
            empty($data['difficulty']) ||
            empty($data['vegetarian']) ||
            empty($data['rate'])
        )
        {
            return ['status' => 400, 'message' => 'Missing required fields'];
        }

        $stmt = $this->db->prepare('INSERT INTO data(name, prep_time, difficulty, vegetarian, rate) VALUES (?, ?, ?, ?, ?)');
        if (!$stmt) 
        {
            return ['status' => 500, 'message' => 'Query preparation failed'];
        }

        $stmt->bind_param('sssss', $data['name'], $data['prep_time'], $data['difficulty'], $data['vegetarian'], $data['rate']);

        if ($stmt->execute()) 
        {
            return ['status' => 200, 'message' => 'Recipe added successfully'];
        } else 
        {
            return ['status' => 500, 'message' => 'Query execution failed'];
        }
    }
    public function getAllRecipes()
    {
        $stmt = $this->db->prepare('SELECT * FROM data');
    
        if ($stmt && $stmt->execute()) 
        {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                return ['status' => 404, 'message' => 'No recipes found'];
            } else {
                return ['status' => 200, 'data' => $result->fetch_all(MYSQLI_ASSOC)];
            }
        } else 
        {
            return ['status' => 500, 'message' => 'Query execution failed'];
        }
    }

    public function getRecipeById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM data WHERE unique_id = ?');
        $stmt->bind_param('s', $id);

        if ($stmt->execute()) 
        {
            $result = $stmt->get_result();

            if ($this->getNumRows($result) === 0) 
            {
                return [
                    'status' => 404,
                    'message' => 'Recipe not found'
                ];
            }

            $recipe = $result->fetch_assoc();
            return [
                'status' => 200,
                'data' => $recipe
            ];
        }

        return [
            'status' => 500,
            'message' => 'Query execution failed'
        ];
    }

    // ✅ Wrapper to make num_rows testable
    protected function getNumRows($result)
    {
        return $result->num_rows;
    }

    public function updateRecipe(array $data): array
    {
        if (!isset($data['unique_id'])) 
        {
            http_response_code(400);
            return [
                'status' => 400,
                'message' => 'unique_id is required'
            ];
        }

        $name = $data['name'] ?? '';
        $prepTime = $data['prep_time'] ?? '';
        $difficulty = $data['difficulty'] ?? '';
        $vegetarian = $data['vegetarian'] ?? '';
        $uniqueId = $data['unique_id'];

        $stmt = $this->db->prepare(
            'UPDATE data SET name=?, prep_time=?, difficulty=?, vegetarian=? WHERE unique_id=?'
        );

        if (!$stmt) 
        {
            http_response_code(500);
            return [
                'status' => 500,
                'message' => 'Failed to prepare statement'
            ];
        }

        $stmt->bind_param('sssss', $name, $prepTime, $difficulty, $vegetarian, $uniqueId);

        if ($stmt->execute()) 
        {
            http_response_code(200);
            return [
                'status' => 200,
                'message' => 'Recipe Updated successfully'
            ];
        }

        http_response_code(500);
        return [
            'status' => 500,
            'message' => 'Query execution failed'
        ];
    }




}
