<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;


class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $feedbacks = DB::table('feedback')
            ->join('users', 'users.id', '=', 'feedback.user_id')
            ->select('feedback.id AS id', 'feedback_content', 'feedback_star', 'name')
            ->get();
        
        if (!is_null($feedbacks)) {
            return response([
                'message' => 'Retrieve All Feedbacks Success',
                'data' => $feedbacks
            ], 200);
        }

        return response([
            'message' => 'Feedback not found',
            'data' => null
        ], 404);
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
            'feedback_content' => 'required',
            'feedback_star' => 'required|numeric',
            'user_id' => 'required|numeric'
        ]);

        if ($validate->fails())
        {
            return response(['message' => $validate->errors()], 400);
        }

        $feedback = Feedback::create($storeData);
        return response([
            'message' => 'Create Feedback Success',
            'data' => $feedback
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
        $feedbacks = Feedback::where('user_id', '=' , $id)->first();
        
        if (!is_null($feedbacks)) {
            return response([
                'message' => 'Retrieve Feedback Success',
                'data' => $feedbacks
            ], 200);
        }

        return response([
            'message' => 'Feedback not found',
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
        $feedback = Feedback::where('user_id', '=' , $id)->first();
        
        if (is_null($feedback))
        {
            return response([
                'message' => 'Feedback Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'feedback_content' => 'required',
            'feedback_star' => 'required|numeric'
        ]);

        if ($validate->fails())
        { 
            return response(['message' => $validate->errors()], 400);
        }

        $feedback->feedback_content = $updateData['feedback_content'];
        $feedback->feedback_star = $updateData['feedback_star'];

        if ($feedback->save()) {
            return response([
                'message' => 'Update Feedback Success',
                'data' => $feedback
            ], 200);
        }

        return response([
            'message' => 'Update Feedback Failed',
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
        $feedback = Feedback::where('user_id', '=' , $id)->first();
        
        if (is_null($feedback)) {
            return response([
                'message' => 'Feedback Not Found',
                'data' => null
            ], 404);
        }

        if($feedback->delete()) {
            return response([
                'message' => 'Delete Feedback Success',
                'data' => $feedback
            ], 200);
        }

        return response([
            'message' => 'Delete Feedback Failed',
            'data' => null,
        ], 400);
    }
}
