<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $items = auth()->user()->todos;
//        sortByDesc('created_at');  auth()->user()->posts
        return response(['data'=>$items],200);
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
        $validator = Validator::make($request->all(), [
            'title'=>'required|min:3|max:50',
            'description'=>'max:100|nullable',
//            'is_completed'=>'prohibited',
        ]);
        if ($validator->fails()) {
            return response([
                'message'=>'An error occurred while attempting to create a todo',
                'error'=>$validator->errors()
            ], 500);
        }else{
            $item = Todo::create($request->all());
            $request->user()->todos()->save($item);
            return response(['data'=>$item], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        //
        $item = Todo::find($todo);
        if ($item){
            return response(['data'=>$item], 200);
        }else{
            return response(['message'=>'A todo with the specified ID was not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        //
        $item = Todo::find($todo);
        // check if item was found
        if(!($item)){
            return response(['message'=>'A todo with the specified ID was not found'], 404);
        }

        // validate request
        $validator = Validator::make($request->all(), [
            'title'=>'required|min:3|max:50',
            'description'=>'max:100|nullable',
            'is_completed'=>'boolean',
        ]);

        if ($validator->fails()) {
            return response([
                'message'=>'An error occurred while attempting to create a todo',
                'error'=>$validator->errors()
            ], 500);
        }
        else{
            $item->update($request->all());
            return response(['data'=>$item], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        //
        $item = Todo::find($todo);
        // check if item was found
        if(!($item)){
            return response(['message'=>'A todo with the specified ID was not found'], 404);
        }else{
            $item->delete();
            return response(['message'=>'Deleted successfully in the database'], 204);
        }
    }

//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param  str  $todo
//     * @return \Illuminate\Http\Response
//     */
//    public function search($todo)
//    {
////        //
////        $items = Todo::where('title', 'like', '%'.$todo.'%');
////        return response(['data'=>$items],200);
//    }
}
