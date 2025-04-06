<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../listrecipe.php';

class RecipeServiceTest extends TestCase
{
    public function testGetAllRecipesReturnsArray()
    {
        $mockConn = $this->createMock(mysqli::class);
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockResult = $this->createMock(mysqli_result::class);
    
        $mockConn->method('prepare')->willReturn($mockStmt);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);
        $mockResult->method('fetch_row')->willReturn(['dummy']);
        $mockResult->method('fetch_all')->willReturn([['id' => 1, 'name' => 'Pizza']]);
    
        $service = new RecipeService($mockConn);
        $result = $service->getAllRecipes();
    
        $this->assertEquals(200, $result['status']);
        $this->assertIsArray($result['data']);
    }
}
