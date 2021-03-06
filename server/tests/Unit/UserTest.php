<?php

use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\User;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_password_is_hashed_when_stored()
    {
        $user = $this->getUnpersistedUser();
        $unhashedPassword = $user['password'];

        $this->post('/users', $user);

        $persistedUser = User::where('email', $user['email'])->first();
        $hashedPassword = $persistedUser->makeVisible('password')->password;

        $this->assertTrue(Hash::check($unhashedPassword, $hashedPassword));
    }
}
