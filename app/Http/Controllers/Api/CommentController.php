<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $comment = Comment::all();
        $comment = Comment::join('users', 'users.id', '=', 'comments.user_id')->select('name', 'comments.id AS id', 'user_id', 'post_id', 'content')->get();

        if (count($comment) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $comment
            ], 200);
        } 

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
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
            'content' => 'required',
            'post_id' => 'required',
            'user_id' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $comment = Comment::create($storeData);
        return response([
            'message' => 'Add Comment Success',
            'data' => $comment
        ], 200);
    }

    public function storeInPost(Request $request, $post_id, $user_id)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'content' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        
        $storeData['post_id'] = $post_id;
        $storeData['user_id'] = $user_id;
        $comment = Comment::create($storeData);
        return response([
            'message' => 'Add Comment Success',
            'data' => $comment
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
        $comment = Comment::find($id);

        if (!is_null($comment)) {
            return response([
                'message' => 'Retrieve Comment Success',
                'data' => $comment
            ], 200);
        }

        return response([
            'message' => 'Comment Not Found',
            'data' => null
        ], 404);
    }

    public function showInPost($post_id)
    {
        $comment = Comment::where('post_id', $post_id)->get(); 

        if (!is_null($comment)) {
            return response([
                'message' => 'Retrieve Comment Success',
                'data' => $comment
            ], 200);
        }

        return response([
            'message' => 'Comment Not Found',
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
        $comment = Comment::find($id);
        if (is_null($comment)) {
            return response([
                'message' => 'Comment Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'content' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $comment->content = $updateData['content'];

        if ($comment->save()) {
            return response([
                'message' => 'Update Comment Success',
                'data' => $comment
            ], 200);
        }
        return response([
            'message' => 'Update Comment Failed',
            'data' => null
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
        $comment = Comment::find($id);

        if (is_null($comment)) {
            return response([
                'message' => 'Comment Not Found',
                'data' => null
            ], 404);
        } 

        if ($comment->delete()) {
            return response([
                'message' => 'Delete Comment Success',
                'data' => $comment
            ], 200);
        }

        return response([
            'message' => 'Delete Comment Failed',
            'data' => null
        ], 400);
    }

    public function destroyPost($idPost)
    {
        $comment = Comment::where('post_id', $idPost);

        if (is_null($comment)) {
            return response([
                'message' => 'Comment Not Found',
                'data' => null
            ], 404);
        } 

        if ($comment->delete()) {
            return response([
                'message' => 'Delete Comment Success',
                'data' => $comment
            ], 200);
        } 

        return response([
            'message' => 'Delete Comment Failed',
            'data' => null
        ], 400); 
    }
}