<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;

class ListsTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithExceptionHandling;

    /** @test */
    public function guests_cannot_hit_lists_endpoints()
    {
        //$this->withoutExceptionHandling()
        $this->get('/lists')->assertResponseStatus(401);
        $this->post('/lists')->assertResponseStatus(401);
        $this->get('/lists/1')->assertResponseStatus(401);
        $this->put('/lists/1')->assertResponseStatus(401);
        $this->delete('/lists/1')->assertResponseStatus(401);
    }

    /** @test */
    public function a_list_must_have_a_valid_name()
    {
        $list = ['not_a_name' => 'Hello!'];

        $this->actingAs($this->user)->post('/lists', $list)->assertResponseStatus(422);
        $response = $this->getDecodedResponse();
        $this->assertArrayHasKey('name', $response);

        $list = ['name' => 'yo'];

        $this->actingAs($this->user)->post('/lists', $list)->assertResponseStatus(422);
        $response = $this->getDecodedResponse();
        $this->assertArrayHasKey('name', $response);
    }

    /** @test */
    public function a_user_can_create_a_list()
    {
        $this->withoutExceptionHandling();

        $list = [
            'name' => 'Home Chores'
        ];

        $this->notSeeInDatabase('tasks_lists', ['name' => $list['name'], 'user_id' => $this->user->id]);

        $this->actingAs($this->user)->post('/lists', $list)->assertResponseStatus(201);

        $this->seeInDatabase('tasks_lists', ['name' => $list['name'], 'user_id' => $this->user->id]);
    }

    /** @test */
    public function creation_endpoint_returns_created_list()
    {
        $this->actingAs($this->user)
            ->post('/lists', ['name' => 'Home Cores']);

        $list = $this->user->lists()->first()->toArray();
        unset($list['deleted_at']);

        $this->seeJsonEquals($list, $this->getDecodedResponse());
    }

    /** @test */
    public function a_list_has_the_right_creator()
    {
        $list['name'] = $this->user->email;

        $this->notSeeInDatabase('tasks_lists', ['name' => $list['name']]);

        $this->actingAs($this->user)->post('/lists', $list);

        $this->seeInDatabase('tasks_lists', [
            'name' => $list['name'],
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function an_authenticated_user_can_request_all_his_lists()
    {
        // We set up the lists for the authenticated user
        factory(\App\TasksList::class, 10)->create(['user_id' => $this->user->id]);

        // Here we add some random lists to other users
        factory(\App\TasksList::class, 10)->create(['user_id' => 1]);
        factory(\App\TasksList::class, 10)->create(['user_id' => 2]);

        $this->actingAs($this->user)->get('/lists');

        $response = $this->getDecodedResponse();
        $lists = $this->user->lists->toArray();

        $this->seeJsonEquals($lists, $response);
    }
}
