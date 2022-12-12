<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Posting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostingController extends Controller
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

        $postings = Posting::create($storeData);
        return response([
            'message' => 'Create Posting Success',
            'data' => $postings
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
        $postings = Posting::where('user_id', '=', $id)->get();

        if (!is_null($postings)) {
            return response([
                'message' => 'Retrieve All Posting Success',
                'data' => $postings
            ], 200);
        }

        return response([
            'message' => 'Posting not found',
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
        $postings = Posting::find($id);
        if (is_null($postings)) {
            return response([
                'message' => 'Posting Not Found',
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

        $postings->post_content = $updateData['post_content'];

        if ($postings->save()) {
            return response([
                'message' => 'Update post Success',
                'data' => $postings
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
        $postings = Posting::find($id);

        if (is_null($postings)) {
            return response([
                'message' => 'Posting Not Found',
                'data' => null
            ], 404);
        }

        if ($postings->delete()) {
            return response([
                'message' => 'Delete Posting Success',
                'data' => $postings
            ], 200);
        }

        return response([
            'message' => 'Delete Posting Failed',
            'data' => null,
        ], 400);
    }
}
