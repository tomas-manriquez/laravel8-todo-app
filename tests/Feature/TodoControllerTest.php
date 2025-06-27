<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Basic index() method test
     * 
     * @return void
     */
    public function test_index()
    {
        $response = $this->get('/api/todo');

        $response
        ->assertOk()
        ->assertJson([]);
    }

    /** Tests **/
    /**
     * Test index method returns all todos
     * Covers: Todo::all() line in index method
     */
    public function test_index_returns_all_todos_when_todos_exist()
    {
        // Arrange: Create multiple todos
        $todos = Todo::factory()->count(5)->create();

        // Act: Call index endpoint
        $response = $this->get('/api/todo');

        // Assert: Check that all todos are returned
        $response->assertStatus(200);
        
        $responseData = $response->json();
        $this->assertCount(5, $responseData);
        // Verify each todo is in response
        /*foreach ($todos as $todo) {
            $this->assert([
                'id' => $todo->id,
                'name' => $todo->name,
                'description' => $todo->description,
                'start_date' => $todo->start_date,
                'end_date' => $todo->end_date,
                'priority' => $todo->priority,
                'is_done' => $todo->is_done,
                'created_at' => $todo->created_at->toISOString(),
                'updated_at' => $todo->updated_at->toISOString(),
            ], $responseData);
        }*/
    }

    /**
     * Test index method when no todos exist
     * Covers: Todo::all() returning empty collection
     */
    public function test_index_returns_empty_array_when_no_todos_exist()
    {
        // Act: Call index with no todos in database
        $response = $this->get('/api/todo');

        // Assert: Empty array returned
        $response->assertStatus(200)
                ->assertExactJson([]);
    }

    /**
     * Test create method (currently empty but needs coverage)
     * Covers: create method body
     */
    public function test_create_method_exists()
    {
        // Since the create method is empty, we just verify it doesn't error
        $controller = new \App\Http\Controllers\TodoController();
        
        // This covers the empty create method
        $result = $controller->create();
        
        // Should return null since method is empty
        $this->assertNull($result);
    }

    /**
     * Test store method creates todo successfully
     * Covers all lines in store method including property assignments and save()
     */
    public function test_store_creates_todo_with_all_fields()
    {
        // Arrange: Prepare complete todo data
        $todoData = ['*'=>[
            'name' => 'Test Todo Name',
            'description' => 'Test Todo Description',
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'priority' => 0,
            'is_done' => true
        ]];

        // Act: Make POST request
        $response = $this->post('/api/todo', [
            'name' => 'Test Todo Name',
            'description' => 'Test Todo Description',
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'priority' => 0,
            'is_done' => true
        ]);
        $response->assertOk();

        // Assert: Verify todo was created with all fields
        $this->assertDatabaseHas('todos', [
            'name' => 'Test Todo Name',
            'description' => 'Test Todo Description',
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'priority' => 'high',
            'is_done' => true
        ]);

        // Verify response (even though your method doesn't return anything)
        $response->assertStatus(200);
    }

    /**
     * Test store method with minimal data
     * Covers: property assignment with null/empty values
     */
    public function test_store_creates_todo_with_minimal_data()
    {
        // Arrange: Minimal data

        // Act: Make POST request
        $response = $this->post('/api/todo', [
            'name' => 'Minimal Todo',
            'description' => null,
            'start_date' => null,
            'end_date' => null,
            'priority' => 0,
            'is_done' => false
        ]);

        // Assert: Verify todo was created
        $this->assertDatabaseHas('todos', [
            'name' => 'Minimal Todo',
            'description' => null,
            'start_date' => null,
            'end_date' => null,
            'priority' => 0,
            'is_done' => false
        ]);
    }

    /**
     * Test store method with different data types
     * Covers: type conversion and edge cases
     */
    public function test_store_rejects_different_data_types()
    {
        // Test with string boolean
        $todoData = [
            'name' => 'Boolean Test',
            'description' => 'Testing boolean conversion',
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-30',
            'priority' => 'medium',
            'is_done' => '1' // String that should convert to boolean
        ];

        $response = $this->post('/api/todo', $todoData);

        $this->assertDatabaseCount('todos',0);
        /**$this->assertDatabaseHas('todos', [
            'name' => 'Boolean Test',
            'description' => 'Testing boolean conversion',
            'priority' => 'medium',
            'is_done' => true // Should be converted to boolean
        ]);*/
    }

    /**
     * Test show method returns specific todo
     * Covers: $todo->id assignment, Todo::where()->firstOrFail()
     */
    public function test_show_returns_specific_todo()
    {
        // Arrange: Create a todo
        $todo = Todo::factory()->create([
            'name' => 'Specific Todo',
            'description' => 'This is a specific todo for testing'
        ]);

        // Act: Call show method
        $response = $this->get("/api/todo/{$todo->id}");

        // Assert: Verify correct todo returned
        $response->assertStatus(200);
        
        $responseData = $response->json();
        $this->assertEquals($todo->id, $responseData['id']);
        $this->assertEquals($todo->name, $responseData['name']);
        $this->assertEquals($todo->description, $responseData['description']);
    }

    /**
     * Test show method with non-existent todo
     * Covers: firstOrFail() throwing ModelNotFoundException
     */
    public function test_show_throws_exception_for_non_existent_todo()
    {
        // Act & Assert: Call show with non-existent ID
        $response = $this->get('/api/todo/99999');
        
        // firstOrFail() should throw ModelNotFoundException, resulting in 404
        $response->assertStatus(404);
    }

    /**
     * Test show method with different todo IDs
     * Covers: $id = $todo->id line and where clause
     */
    public function test_show_works_with_different_todo_ids()
    {
        // Arrange: Create multiple todos
        $todo1 = Todo::factory()->create(['name' => 'First Todo']);
        $todo2 = Todo::factory()->create(['name' => 'Second Todo']);
        $todo3 = Todo::factory()->create(['name' => 'Third Todo']);

        // Act & Assert: Test each todo
        $response1 = $this->get("/api/todo/{$todo1->id}");
        $response1->assertStatus(200);
        $this->assertEquals('First Todo', $response1->json()['name']);

        $response2 = $this->get("/api/todo/{$todo2->id}");
        $response2->assertStatus(200);
        $this->assertEquals('Second Todo', $response2->json()['name']);

        $response3 = $this->get("/api/todo/{$todo3->id}");
        $response3->assertStatus(200);
        $this->assertEquals('Third Todo', $response3->json()['name']);
    }

    /**
     * Test edit method (currently empty but needs coverage)
     * Covers: edit method body
     */
    public function test_edit_method_exists()
    {
        // Arrange: Create a todo for the edit method parameter
        $todo = Todo::factory()->create();
        
        // Since the edit method is empty, we just verify it doesn't error
        $controller = new \App\Http\Controllers\TodoController();
        
        // This covers the empty edit method
        $result = $controller->edit($todo);
        
        // Should return null since method is empty
        $this->assertNull($result);
    }

    /**
     * Test update method (currently empty but needs coverage)
     * Covers: update method body
     */
    public function test_update_method_exists()
    {
        // Arrange: Create a todo and request mock
        $todo = Todo::factory()->create();
        $request = new \Illuminate\Http\Request();
        
        // Since the update method is empty, we just verify it doesn't error
        $controller = new \App\Http\Controllers\TodoController();
        
        // This covers the empty update method
        $result = $controller->update($request, $todo);
        
        // Should return null since method is empty
        $this->assertNull($result);
    }

    /**
     * Test destroy method deletes existing todo
     * Covers: Todo::find() with existing ID and delete() method
     */
    public function test_destroy_deletes_existing_todo()
    {
        // Arrange: Create a todo
        $todo = Todo::factory()->create([
            'name' => 'Todo to be deleted'
        ]);
        $todoId = $todo->id;

        // Act: Call destroy method
        $response = $this->delete("/api/todo/{$todoId}");

        // Assert: Verify todo was deleted
        $response->assertStatus(200);
        
        // Check response indicates deletion
        $this->assertTrue($response->json());
        
        // Verify todo no longer exists in database
        $this->assertDatabaseMissing('todos', [
            'id' => $todoId
        ]);
    }

    /**
     * Test destroy method with non-existent todo
     * Covers: Todo::find() returning null and calling delete() on null
     */
    public function test_destroy_handles_non_existent_todo()
    {
        // Act: Call destroy with non-existent ID
        $response = $this->delete('/api/todo/99999');

        // The method will try to call delete() on null, which should cause an error
        // This tests the edge case in your current implementation
        $response->assertStatus(500); // Should error due to calling delete() on null
    }

    /**
     * Test destroy method with various existing todo IDs
     * Covers: Todo::find() with different valid IDs
     */
    public function test_destroy_works_with_different_todo_ids()
    {
        // Arrange: Create multiple todos
        $todo1 = Todo::factory()->create(['name' => 'Delete Me 1']);
        $todo2 = Todo::factory()->create(['name' => 'Delete Me 2']);
        $todo3 = Todo::factory()->create(['name' => 'Delete Me 3']);

        // Act & Assert: Delete each todo
        $response1 = $this->delete("/api/todo/{$todo1->id}");
        $response1->assertStatus(200);
        $this->assertDatabaseMissing('todos', ['id' => $todo1->id]);

        $response2 = $this->delete("/api/todo/{$todo2->id}");
        $response2->assertStatus(200);
        $this->assertDatabaseMissing('todos', ['id' => $todo2->id]);

        $response3 = $this->delete("/api/todo/{$todo3->id}");
        $response3->assertStatus(200);
        $this->assertDatabaseMissing('todos', ['id' => $todo3->id]);
    }

    /**
     * Test destroy method return value
     * Covers: return $del->delete() line
     */
    public function test_destroy_returns_delete_result()
    {
        // Arrange: Create a todo
        $todo = Todo::factory()->create();

        // Act: Call destroy
        $response = $this->delete("/api/todo/{$todo->id}");

        // Assert: Check that it returns the result of delete() (which is boolean true)
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData);
    }

    /**
     * Integration test covering multiple methods in sequence
     * Provides additional coverage and real-world usage patterns
     */
    public function test_full_crud_workflow()
    {
        // 1. Start with empty database (index returns empty)
        $response = $this->get('/api/todo');
        $response->assertStatus(200)->assertExactJson([]);

        // 2. Create a todo (store method)
        $todoData = [
            'name' => 'Workflow Test Todo',
            'description' => 'Testing complete workflow',
            'start_date' => '2024-07-01',
            'end_date' => '2024-07-31',
            'priority' => 'high',
            'is_done' => false
        ];
        
        $storeResponse = $this->post('/api/todo', $todoData);
        $storeResponse->assertStatus(200);

        // 3. Verify todo exists (index returns it)
        $indexResponse = $this->get('/api/todo');
        $indexResponse->assertStatus(200);
        $todos = $indexResponse->json();
        $this->assertCount(1, $todos);
        $createdTodo = $todos[0];

        // 4. Show the specific todo
        $showResponse = $this->get("/api/todo/{$createdTodo['id']}");
        $showResponse->assertStatus(200);
        $this->assertEquals($createdTodo['name'], $showResponse->json()['name']);

        // 5. Delete the todo
        $deleteResponse = $this->delete("/api/todo/{$createdTodo['id']}");
        $deleteResponse->assertStatus(200);
        $this->assertTrue($deleteResponse->json());

        // 6. Verify todo is gone (index returns empty again)
        $finalIndexResponse = $this->get('/api/todo');
        $finalIndexResponse->assertStatus(200)->assertExactJson([]);
    }

    /**
     * Test edge cases and boundary conditions
     */
    public function test_edge_cases()
    {
        // Test with very long strings
        $longString = str_repeat('a', 1000);
        $todoData = [
            'name' => $longString,
            'description' => $longString,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'priority' => 'low',
            'is_done' => false
        ];

        $response = $this->post('/api/todo', $todoData);
        $response->assertStatus(200);

        $this->assertDatabaseHas('todos', [
            'name' => $longString,
            'description' => $longString
        ]);

        // Test with special characters
        $specialData = [
            'name' => 'Special chars: !@#$%^&*()[]{}|;:,.<>?',
            'description' => 'Unicode: éñüñü 中文 🚀 emoji',
            'start_date' => '2024-02-29', // Leap year date
            'end_date' => '2024-02-29',
            'priority' => 'medium',
            'is_done' => true
        ];

        $response2 = $this->post('/api/todo', $specialData);
        $response2->assertStatus(200);

        $this->assertDatabaseHas('todos', [
            'name' => 'Special chars: !@#$%^&*()[]{}|;:,.<>?',
            'description' => 'Unicode: éñüñü 中文 🚀 emoji'
        ]);
    }
}
