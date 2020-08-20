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
    public function index(int $list, Request $request)
    {
        return response()->json(
            $request->user()->lists()->findOrFail($list)->tasks
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $task
     * @return \Illuminate\Http\Response
     */
    public function show(int $task)
    {
        //
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
