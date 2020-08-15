<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;

class UsersTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithExceptionHandling;

    /** @test */
    public function can_create_user_with_valid_data()
    {
        $user = $this->getUnpersistedUser();

        $response = $this->post('/users', $user);

        unset($user['password']);

        $response->assertResponseStatus(201);
        $this->seeInDatabase('users', $user);
    }

    /** @test */
    public function successful_creation_returns_user_data()
    {
        $user = $this->getUnpersistedUser();

        $this->post('/users', $user);

        $response = json_decode($this->response->getContent(), true);

        $desiredStructure = [
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ];

        $this->assertArrayStructure($desiredStructure, $response);
    }
}
