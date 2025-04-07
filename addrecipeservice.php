<?php

class RecipeService
{
    private $db;

    public function __construct(mysqli $conn)
    {
        $this->db = $conn;
    }

    public function addRecipe($data)
    {
        if (!isset($data['name'], $data['prep_time'], $data['difficulty'], $data['vegetarian'], $data['rate'])) {
            return ['status' => 400, 'message' => 'Invalid input'];
        }

        $stmt = $this->db->prepare(
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
