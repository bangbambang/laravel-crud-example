<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Response;

class CategoryEndpointTest extends TestCase
{
    use WithFaker;

    public function test_index_handler()
    {
        $response = $this->get('/api/categories');
        $response->assertStatus(200);
    }

    public function test_show_handler()
    {
        $response = $this->get('/api/categories/1');
        $response->assertStatus(200);
    }

    public function test_show_error_nonexistent_id()
    {
        $response = $this->get('/api/categories/9999');
        $response->assertStatus(404);
    }

    public function test_show_error_invalid_id()
    {
        $response = $this->get('/api/categories/something');
        $response->assertStatus(404);
    }

    public function test_store_success()
    {
        $response = $this->postJSON('/api/categories', [
            'name'  => 'foo',
            'enable'=> true,
        ]);
        $response->assertStatus(Response::HTTP_CREATED)
        ->assertJson([
            'status' => 'OK',
            'data' => [
                'name'  => 'foo',
                'enable'=> true,
            ]
        ])
        ->assertJsonPath('data.id', fn ($id) => is_int($id) && $id > 1);
    }

    public function test_store_fails_required_field_missing()
    {
        $response = $this->postJSON('/api/categories', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'The name field is required. (and 1 more error)',
            'errors' => [
                'name' => [
                    'The name field is required.'
                ],
                'enable' => [
                    'The enable field is required.'
                ],
            ]
        ]);
    }

    public function test_store_fails_validation_constraint()
    {
        $response = $this->postJSON('/api/categories', [
            // 'name' => $this->faker->text(300),
            // use hardcoded string because faker is rather unreliable in generating text with specified length
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at dolor nulla. Suspendisse urna nisi, mollis ac laoreet ut, bibendum a nibh. Aenean aliquet commodo tortor, quis convallis tortor venenatis eu. Aliquam dapibus dignissim arcu vitae scelerisque quam.',
            'enable' => $this->faker->word(),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'The name must not be greater than 255 characters. (and 1 more error)',
            'errors' => [
                'name' => [
                    'The name must not be greater than 255 characters.'
                ],
                'enable' => [
                    'The enable field must be true or false.'
                ],
            ]
        ]);
    }

    public function test_update_success()
    {
        $response = $this->putJSON('/api/categories/1', [
            'name'=> 'new name',
        ]);
        $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'status' => 'OK',
            'data' => [
                'name'=> 'new name',
            ]
        ]);
    }

    public function test_update_fails_without_id()
    {
        $response = $this->putJSON('/api/categories', [
            'name'=> 'new name',
        ]);
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_update_fails_validation_constraint()
    {
        $response = $this->putJSON('/api/categories/1', [
            'name' => '',
            'enable' => null,
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'The name field must have a value. (and 1 more error)',
            'errors' => [
                'name' => [
                    'The name field must have a value.'
                ],
                'enable' => [
                    'The enable field must have a value.'
                ],
            ]
        ]);
    }

    public function test_destroy_success()
    {
        $response = $this->deleteJSON('/api/categories/1');
        $response->assertStatus(Response::HTTP_OK);
    }
}
