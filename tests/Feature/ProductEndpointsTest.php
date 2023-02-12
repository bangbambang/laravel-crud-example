<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductEndpointTest extends TestCase
{
    use WithFaker;

    public function test_index_handler()
    {
        $response = $this->get('/api/products');
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_show_handler()
    {
        $response = $this->get('/api/products/1');
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_show_error_nonexistent_id()
    {
        $response = $this->get('/api/products/9999');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_show_error_invalid_id()
    {
        $response = $this->get('/api/products/something');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_store_success()
    {
        $data = [
            'name'  => $this->faker->word(),
            'description'  => $this->faker->paragraph(),
            'enable'=> true,
            'images'=> [1,2,3],
            'categories'=> [1,2,3],
        ];
        $response = $this->postJSON('/api/products', $data);
        $response
            ->assertValid()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'status' => 'OK',
                'data' => [
                    'name'  => $data['name'],
                    'description'  => $data['description'],
                    'enable'=> true,
                ]
            ])
            ->assertJsonPath('data.id', fn ($id) => is_int($id) && $id > 1);
    }

    public function test_store_fails_required_field_missing()
    {
        $response = $this->postJSON('/api/products', []);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['name', 'description', 'enable', 'categories', 'images']);
    }

    public function test_store_fails_validation_constraint()
    {
        $data = [
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at dolor nulla. Suspendisse urna nisi, mollis ac laoreet ut, bibendum a nibh. Aenean aliquet commodo tortor, quis convallis tortor venenatis eu. Aliquam dapibus dignissim arcu vitae scelerisque quam.',
            'enable' => $this->faker->word(),
            'images' => $this->faker->imageURL(),
            'categories' => 1,
        ];
        $response = $this->postJSON('/api/products', $data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['name', 'enable']);
    }

    public function test_update_success()
    {
        $data = ['name'=> 'new name'];
        $response = $this->putJSON('/api/products/1', $data);
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
        $response = $this->putJSON('/api/products', $data);
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_update_fails_nonexistent_id()
    {
        $data = ['name'=> 'new name'];
        $response = $this->putJSON('/api/products/999', $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_fails_validation_constraint()
    {
        $data = ['name' => '', 'enable' => null];
        $response = $this->putJSON('/api/products/1', $data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['name', 'enable']);
    }

    public function test_destroy_success()
    {
        $response = $this->deleteJSON('/api/products/9');
        $response->assertStatus(Response::HTTP_OK);
    }
}
