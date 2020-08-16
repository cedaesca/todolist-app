<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Support\Facades\Hash;

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
        $secondUser = $this->getUnpersistedUser();
        $secondUser['email'] = $this->user->email;

        $this->post('/users', $secondUser)->assertResponseStatus(422);
    }

    /** @test */
    public function successful_creation_returns_user_data()
    {
        $user = $this->getUnpersistedUser();

        $this->post('/users', $user);

        $response = $this->getDecodedResponse();

        $desiredStructure = [
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ];

        $this->assertArrayStructure($desiredStructure, $response);
    }

    /** @test */
    public function existing_user_can_login()
    {
        $credentials = [
            'email' => $this->user->email,
            'password' => 'password'
        ];

        $this->post('/auth/login', $credentials)->assertResponseStatus(200);

        $desiredStructure = [
            'token',
            'token_type',
            'expires_in'
        ];

        $response = $this->getDecodedResponse();

        $this->assertArrayStructure($desiredStructure, $response);
    }

    /** @test */
    public function unexistent_user_cannot_login()
    {
        $credentials = [
            'email' => 'unexistent@user.com',
            'password' => 'password'
        ];

        $this->post('/auth/login', $credentials)->assertResponseStatus(401);
    }

    /** @test */
    public function guests_cannot_access_users_routes_aside_from_store()
    {
        $this->get('/users/me')->assertResponseStatus(401);
        $this->put('/users/me')->assertResponseStatus(401);
        $this->delete('/users/me')->assertResponseStatus(401);
    }

    /** @test */
    public function me_route_returns_authenticated_user_details()
    {
        $response = $this->actingAs($this->user)->get('/users/me');

        $response->assertResponseStatus(200);
        $response->seeJsonEquals($this->user->toArray());
    }

    /** @test */
    public function user_can_update_his_information()
    {
        $newData = [
            'name' => 'CompletelyUniqueName',
            'password' => 'newPassword'
        ];

        $response = $this->actingAs($this->user)->put('/users/me', $newData);

        $this->user->name = $newData['name'];

        // We verify the response gives the updated data
        $response->seeJsonEquals($this->user->toArray());

        // Now we assert the data was updated in the database
        $whereConstraints = [
            'email' => $this->user->email,
            'name' => $this->user->name
        ];

        $this->assertTrue(\App\User::where($whereConstraints)->exists());

        // We verify if the password was correctly updated and hashed
        $this->user->fresh();
        $this->user->makeVisible('password');

        $this->assertTrue(Hash::check($newData['password'], $this->user->password));
    }

    /** @test */
    public function user_can_delete_his_account()
    {
        $response = $this->actingAs($this->user)->delete('/users/me', [
            'confirmation' => $this->user->email
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonEquals($this->user->toArray());

        $this->notSeeInDatabase('users', ['email' => $this->user->email]);
    }

    /** @test */
    public function user_cannot_delete_his_account_without_valid_confirmation()
    {
        $this->actingAs($this->user)->delete('/users/me')->assertResponseStatus(422);

        $this->actingAs($this->user)->delete('/users/me', [
            'confirmation' => 'someRandomStuff'
        ])->assertResponseStatus(422);
    }
}
