<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'post_content' => 'required',
            'user_id' => 'required|numeric'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $post = Post::create($storeData);
        return response([
            'message' => 'Create Post Success',
            'data' => $post
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $posts = Post::where('user_id', '=', $id)->get();

        if (!is_null($posts)) {
            return response([
                'message' => 'Retrieve All posts Success',
                'data' => $posts
            ], 200);
        }

        return response([
            'message' => 'posts not found',
            'data' => null
        ], 404);
    }

    public function showAll()
    {
        $posts = DB::table('users')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->select('posts.id AS id', 'post_content', 'user_id', 'name')
            ->get();

        if (!is_null($posts)) {
            return response([
                'message' => 'Retrieve All posts Success',
                'data' => $posts
            ], 200);
        }

        return response([
            'message' => 'posts not found',
            'data' => null
        ], 404);
    }

    public function showUserPosts($id)
    {
        $posts = DB::table('users')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->select('posts.id AS id', 'post_content', 'user_id', 'name')
            ->where('user_id', '=', $id)
            ->get();

//        $userPosts = $posts
//            ->where('user_id', '=', $id)
//            ->get();

        if (!is_null($posts)) {
            return response([
                'message' => 'Retrieve User posts Success',
                'data' => $posts
            ], 200);
        }

        return response([
            'message' => 'posts not found',
            'data' => null
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $post = Post::find($id);
        if (is_null($post)) {
            return response([
                'message' => 'Post Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'post_content' => 'required'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $post->post_content = $updateData['post_content'];

        if ($post->save()) {
            return response([
                'message' => 'Update post Success',
                'data' => $post
            ], 200);
        }

        return response([
            'message' => 'Update post Failed',
            'data' => null,
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $post = Post::find($id);

        if (is_null($post)) {
            return response([
                'message' => 'Post Not Found',
                'data' => null
            ], 404);
        }

        if ($post->delete()) {
            return response([
                'message' => 'Delete post Success',
                'data' => $post
            ], 200);
        }

        return response([
            'message' => 'Delete post Failed',
            'data' => null,
        ], 400);
    }
}
