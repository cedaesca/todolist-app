<?php

namespace App\Http\Controllers;

use App\Task;
use App\TasksList;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $list
     * @return \Illuminate\Http\Response
     */
    public function index(int $list)
    {
        $list = TasksList::findOrFail($list);

        $this->authorize('view', $list);

        return response()->json($list->tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|int|min:1',
            'description' => 'required|string|min:3'
        ]);

        $list = TasksList::findOrFail($request->list_id);

        $this->authorize('create-task', $list);

        $task = $list->tasks()->create($request->all());

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $task
     * @return \Illuminate\Http\Response
     */
    public function show(int $list, int $task, Request $request)
    {
        $task = $request->user()
            ->lists()
            ->findOrFail($list)
            ->tasks()
            ->findOrFail($task);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $task)
    {
        //
    }
}
