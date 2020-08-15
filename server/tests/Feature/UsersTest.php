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
    public function cannot_create_user_with_invalid_data()
    {
        // Testing with single attributes
        $this->post('/users', [])->assertResponseStatus(422);
        $this->post('/users', ['name' => 'Juanito'])->assertResponseStatus(422);
        $this->post('/users', ['password' => '123456789'])->assertResponseStatus(422);
        $this->post('/users', ['email' => 'john@example.com'])->assertResponseStatus(422);

        // Testing in combination of two attributes
        $this->post('/users', [
            'name' => 'John Doe',
            'email' => 'john@doe.com'
        ])->assertResponseStatus(422);

        $this->post('/users', [
            'name' => 'John Doe',
            'email' => 'john@doe.com'
        ])->assertResponseStatus(422);

        $this->post('/users', [
            'email' => 'john@doe.com',
            'password' => '123456789'
        ])->assertResponseStatus(422);

        // Testing with all attributes but invalid syntax
        $this->post('/users', [
            'name' => 'John Doe',
            'password' => '12345',
            'email' => 'john@doe.com'
        ])->assertResponseStatus(422);

        $this->post('/users', $user = [
            'name' => 'John Doe',
            'password' => '123456',
            'email' => 'johndoecom'
        ])->assertResponseStatus(422);
    }

    /** @test */
    public function emails_must_be_unique()
    {
        $firstUser = factory(\App\User::class)->create();

        $secondUser = $this->getUnpersistedUser();
        $secondUser['email'] = $firstUser->email;

        $this->post('/users', $secondUser)->assertResponseStatus(422);
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
