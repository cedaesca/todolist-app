<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Tests\Traits\AssertArrayStructure;

abstract class TestCase extends BaseTestCase
{
    use AssertArrayStructure;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    /**
     * Returns an array with the unpersisted
     * User Data
     * 
     * @return array
     */
    protected function getUnpersistedUser(): array
    {
        $user = factory(\App\User::class)->make(['password' => 'password']);
        $user = $user->makeVisible('password')->toArray();

        unset($user['email_verified_at']);

        return $user;
    }

    /**
     * Returns a decoded response
     * 
     * @return array
     */
    protected function getDecodedResponse(): array
    {
        return json_decode($this->response->getContent(), true);
    }
}
