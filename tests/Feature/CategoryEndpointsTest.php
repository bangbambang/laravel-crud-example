<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CategoryEndpointTest extends TestCase
{
    use WithFaker;

    public function test_index_handler()
    {
        $response = $this->get('/api/categories');
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_show_handler()
    {
        $response = $this->get('/api/categories/1');
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_show_error_nonexistent_id()
    {
        $response = $this->get('/api/categories/9999');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_show_error_invalid_id()
    {
        $response = $this->get('/api/categories/something');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_store_success()
    {
        $data = ['name'  => 'foo', 'enable'=> true];
        $response = $this->postJSON('/api/categories', $data);
        $response
            ->assertValid()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'status' => 'OK',
                'data' => $data
            ])
            ->assertJsonPath('data.id', fn ($id) => is_int($id) && $id > 1);
    }

    public function test_store_fails_required_field_missing()
    {
        $response = $this->postJSON('/api/categories', []);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['name', 'enable']);
    }

    public function test_store_fails_validation_constraint()
    {
        $data = [
            // 'name' => $this->faker->text(300),
            // use hardcoded string because faker is rather unreliable in generating text with specified length
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at dolor nulla. Suspendisse urna nisi, mollis ac laoreet ut, bibendum a nibh. Aenean aliquet commodo tortor, quis convallis tortor venenatis eu. Aliquam dapibus dignissim arcu vitae scelerisque quam.',
            'enable' => $this->faker->word(),
        ];
        $response = $this->postJSON('/api/categories', $data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['name', 'enable']);
    }

    public function test_update_success()
    {
        $data = ['name'=> 'new name'];
        $response = $this->putJSON('/api/categories/1', $data);
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertValid()
            ->assertJson([
                'status' => 'OK',
                'data' => $data
            ]);
    }

    public function test_update_fails_without_id()
    {
        $data = ['name'=> 'new name'];
        $response = $this->putJSON('/api/categories', $data);
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_update_fails_nonexistent_id()
    {
        $data = ['name'=> 'new name'];
        $response = $this->putJSON('/api/categories/999', $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_fails_validation_constraint()
    {
        $data = ['name' => '', 'enable' => null];
        $response = $this->putJSON('/api/categories/1', $data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['name', 'enable']);
    }

    public function test_destroy_success()
    {
        $response = $this->deleteJSON('/api/categories/9');
        $response->assertStatus(Response::HTTP_OK);
    }
}
