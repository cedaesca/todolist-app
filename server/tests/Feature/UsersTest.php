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
        $user = factory(\App\User::class)->create();

        $credentials = [
            'email' => $user->email,
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
        factory(\App\User::class, 5)->create();

        $user = factory(\App\User::class)->create();

        factory(\App\User::class, 5)->create();

        $response = $this->actingAs($user)->get('/users/me');

        $response->assertResponseStatus(200);
        $response->seeJsonEquals($user->toArray());
    }

    /** @test */
    public function user_can_update_his_information()
    {
        factory(\App\User::class, 20)->create();
        $user = factory(\App\User::class)->create();

        $newData = [
            'name' => 'CompletelyUniqueName',
            'password' => 'newPassword'
        ];

        $response = $this->actingAs($user)->put('/users/me', $newData);

        $user->name = $newData['name'];

        // We verify the response gives the updated data
        $response->seeJsonEquals($user->toArray());

        // Now we assert the data was updated in the database
        $whereConstraints = [
            'email' => $user->email,
            'name' => $user->name
        ];

        $this->assertTrue(\App\User::where($whereConstraints)->exists());

        // We verify if the password was correctly updated and hashed
        $user->fresh();
        $user->makeVisible('password');

        $this->assertTrue(Hash::check($newData['password'], $user->password));
    }

    /** @test */
    public function user_can_delete_his_account()
    {
        factory(\App\User::class, 10)->create();
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->delete('/users/me', [
            'confirmation' => $user->email
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonEquals($user->toArray());

        $this->notSeeInDatabase('users', ['email' => $user->email]);
    }

    /** @test */
    public function user_cannot_delete_his_account_without_valid_confirmation()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)->delete('/users/me')->assertResponseStatus(422);

        $this->actingAs($user)->delete('/users/me', [
            'confirmation' => 'someRandomStuff'
        ])->assertResponseStatus(422);
    }
}
