<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_create_user_with_valid_data()
    {
        $user = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123456789'
        ];

        $response = $this->post('/users', $user);

        $response->assertResponseStatus(201);
        $this->seeInDatabase('users', $user);
    }
}
