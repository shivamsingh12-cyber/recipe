<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../RecipeService.php';


class RecipeServiceTest extends TestCase
{
    public function testGetAllRecipesReturnsArray()
    {
        // Mock result with fetch_all and num_rows
        $mockResult = $this->getMockBuilder(stdClass::class)
                           ->addMethods(['fetch_all'])
                           ->addMethods(['num_rows'])
                           ->getMock();
        $mockResult->method('fetch_all')->willReturn([
            ['id' => 1, 'name' => 'Test Recipe']
        ]);
        // For num_rows, we fake it as a public property instead of a method
        $mockResult->num_rows = 1;
    
        // Mock statement to return our result
        $mockStmt = $this->getMockBuilder(stdClass::class)
                         ->addMethods(['execute', 'get_result'])
                         ->getMock();
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);
    
        // Mock mysqli to return our mock statement
        $mockDb = $this->getMockBuilder(stdClass::class)
                       ->addMethods(['prepare'])
                       ->getMock();
        $mockDb->method('prepare')->willReturn($mockStmt);
    
        require_once __DIR__ . '/../RecipeService.php';
        $recipeService = new RecipeService($mockDb);
    
        $result = $recipeService->getAllRecipes();
    
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status']);
        $this->assertArrayHasKey('data', $result);
    }
    

    public function testAddRecipeSuccess()
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->expects($this->once())->method('bind_param');
        $mockStmt->expects($this->once())->method('execute')->willReturn(true);

        $mockConn = $this->createMock(mysqli::class);
        $mockConn->method('prepare')->willReturn($mockStmt);

        $service = new RecipeService($mockConn);
        $result = $service->addRecipe([
            'name' => 'Pasta',
            'prep_time' => '20 min',
            'difficulty' => 'Easy',
            'vegetarian' => 'Yes',
            'rate' => '5'
        ]);

        $this->assertEquals(200, $result['status']);
    }

    public function testAddRecipeFailsOnMissingFields()
    {
        $mockConn = $this->createMock(mysqli::class);
        $service = new RecipeService($mockConn);
        $result = $service->addRecipe(['name' => 'Pasta']);

        $this->assertEquals(400, $result['status']);
    }

    public function testAddRecipeFailsOnExecution()
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param');
        $mockStmt->method('execute')->willReturn(false);

        $mockConn = $this->createMock(mysqli::class);
        $mockConn->method('prepare')->willReturn($mockStmt);

        $service = new RecipeService($mockConn);
        $result = $service->addRecipe([
            'name' => 'Pizza',
            'prep_time' => '30 min',
            'difficulty' => 'Medium',
            'vegetarian' => 'No',
            'rate' => '4'
        ]);

        $this->assertEquals(500, $result['status']);
    }

    public function testGetRecipeByIdSuccess()
    {
        $mockConn = $this->createMock(mysqli::class);
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockResult = $this->createMock(mysqli_result::class);
    
        $mockConn->method('prepare')->willReturn($mockStmt);
        $mockStmt->expects($this->once())->method('bind_param');
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);
    
        $mockResult->method('fetch_assoc')->willReturn([
            'unique_id' => '1',
            'name' => 'Test Recipe',
            'prep_time' => '15',
            'difficulty' => 'Easy',
            'vegetarian' => 'Yes',
            'rate' => '5'
        ]);
    
        // ✅ Partial mock with overridden getNumRows
        $service = $this->getMockBuilder(RecipeService::class)
            ->setConstructorArgs([$mockConn])
            ->onlyMethods(['getNumRows'])
            ->getMock();
    
        $service->method('getNumRows')->willReturn(1);
    
        $response = $service->getRecipeById('1');
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Test Recipe', $response['data']['name']);
    }
    

    public function testGetRecipeByIdNotFound()
    {
        $mockConn = $this->createMock(mysqli::class);
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockResult = $this->createMock(mysqli_result::class);
    
        $mockConn->method('prepare')->willReturn($mockStmt);
        $mockStmt->expects($this->once())->method('bind_param');
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);
    
       
        $service = $this->getMockBuilder(RecipeService::class)
            ->setConstructorArgs([$mockConn])
            ->onlyMethods(['getNumRows'])  // 👈 override just this method
            ->getMock();
    
        $service->method('getNumRows')->willReturn(0);
    
        $response = $service->getRecipeById('999');
        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Recipe not found', $response['message']);
    }
    
    public function testGetRecipeByIdQueryFail()
    {
        $mockConn = $this->createMock(mysqli::class);
        $mockStmt = $this->createMock(mysqli_stmt::class);

        $mockConn->method('prepare')->willReturn($mockStmt);
        $mockStmt->method('bind_param');
        $mockStmt->method('execute')->willReturn(false);

        $service = new RecipeService($mockConn);
        $response = $service->getRecipeById('1');

        $this->assertEquals(500, $response['status']);
        $this->assertEquals('Query execution failed', $response['message']);
    }

    public function testUpdateRecipeSuccess()
{
    $mockConn = $this->createMock(mysqli::class);
    $mockStmt = $this->createMock(mysqli_stmt::class);

    $mockConn->expects($this->once())
             ->method('prepare')
             ->with('UPDATE data SET name=?, prep_time=?, difficulty=?, vegetarian=? WHERE unique_id=?')
             ->willReturn($mockStmt);

    $mockStmt->expects($this->once())
             ->method('bind_param')
             ->with('sssss', 'Salad', '5', 'easy', 'yes', '1');

    $mockStmt->expects($this->once())
             ->method('execute')
             ->willReturn(true);

    $service = new RecipeService($mockConn);

    $data = [
        'name' => 'Salad',
        'prep_time' => '5',
        'difficulty' => 'easy',
        'vegetarian' => 'yes',
        'unique_id' => '1'
    ];

    $response = $service->updateRecipe($data);

    $this->assertEquals(200, $response['status']);
    $this->assertEquals('Recipe Updated successfully', $response['message']);
}

public function testUpdateRecipeFailsWithoutUniqueId()
{
    $mockConn = $this->createMock(mysqli::class);
    $service = new RecipeService($mockConn);

    $data = [
        'name' => 'Salad',
        'prep_time' => '5',
        'difficulty' => 'easy',
        'vegetarian' => 'yes'
    ];

    $response = $service->updateRecipe($data);

    $this->assertEquals(400, $response['status']);
    $this->assertEquals('unique_id is required', $response['message']);
}

public function testUpdateRecipeExecutionFails()
{
    $mockConn = $this->createMock(mysqli::class);
    $mockStmt = $this->createMock(mysqli_stmt::class);

    $mockConn->expects($this->once())
             ->method('prepare')
             ->willReturn($mockStmt);

    $mockStmt->expects($this->once())
             ->method('bind_param');

    $mockStmt->expects($this->once())
             ->method('execute')
             ->willReturn(false);

    $service = new RecipeService($mockConn);

    $data = [
        'name' => 'Salad',
        'prep_time' => '5',
        'difficulty' => 'easy',
        'vegetarian' => 'yes',
        'unique_id' => '1'
    ];

    $response = $service->updateRecipe($data);

    $this->assertEquals(500, $response['status']);
    $this->assertEquals('Query execution failed', $response['message']);
}

}
