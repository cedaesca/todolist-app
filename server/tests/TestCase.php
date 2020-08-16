<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Tests\Traits\AssertArrayStructure;

abstract class TestCase extends BaseTestCase
{
    use AssertArrayStructure;

    /**
     * User to be used when authentication is needed
     * 
     */
    protected $user;

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
     * PHP Unit setup method
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        factory(\App\User::class, 5)->create();
        $this->user = factory(\App\User::class)->create();
        factory(\App\User::class, 5)->create();
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
