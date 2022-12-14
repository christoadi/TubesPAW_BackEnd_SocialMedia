<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index() {
        $users = User::all(); //Collecting all user data

        if (count($users) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $users
            ], 200);
        } //return all user data

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); //return if empty
    }

    //register
    public function register(Request $request){
        $registrationData = $request->all();
        $validate = Validator::make($registrationData, [ //input validation
            'name' => 'required',
            'gender' => 'required',
            'dateborn' => 'required|date_format:Y-m-d', //date format
            'email' => 'required|email:rfc,dns|unique:users', //rfc,dns for email input //unique input
            'username' => 'required|unique:users', //unique input
            'password' => 'required'
        ]); 

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return validation if error in input

        $registrationData['password'] = bcrypt($request->password); //encrypting password
        $user = User::create($registrationData); //creating user data

        event(new Registered($user)); //Registering user

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200); //return user data
    }
    

    //Log in
    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [ //input validation
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if ($validate->fails()){
            return response(['message' => $validate->errors()], 400); //return validation if error in input
        }    

        if (!Auth::attempt($loginData)){
            return response(['message' => 'Invalid Credentials'], 401); //return validation if failed log in
        }    

        /** @var \App\Models\User $user **/
        $user = Auth::User();
        if($user->email_verified_at != null){
            $token = $user->createToken('Authentication Token')->accessToken; //generate token
        }else{
            return response(['message'=>'Email Not verified!'],401);
        }    
        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]); //return user data and token in json format
    }

    //show spesific user by id
    public function show($id){
        $users = User::find($id); //find user by id

        if (!is_null($users)) {
            return response([
                'message' => 'Retrive User Success',
                'data' => $users
            ], 200); //return user data by id
        }

        return response([
            'message' => 'User Not Found',
            'data' => null
        ], 404); //return if user by id not found 
    }

    //Update
    public function update(Request $request, $id){
        $users = User::find($id); //find user by id
        if(is_null($users)){
            return response([
                'message'=>'User Not Found',
                'data'=>null
            ],400); //return if user by id not found
        }

        $updateData = $request->all(); //Validate new data
        $validate = Validator::make($updateData, [ 
            'name' => 'required',
            'gender' => 'required',
            'dateborn' => 'required|date_format:Y-m-d',
            'username' => ['required',Rule::unique('users')->ignore($users)],
            'password' => ''
        ]); // validasi data

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        //replacing old data with the newest one
        $users->name = $updateData['name'];
        $users->gender = $updateData['gender'];
        $users->dateborn = $updateData['dateborn'];
        $users->username = $updateData['username'];
        if ($updateData['password'] != ""){
            $users->password = bcrypt($updateData['password']);
        }

        if($users->save()){
            return response([
                'message'=> 'Update User Success',
                'data'=>$users
            ],200);
        } //return if data has been updated
        return response([
            'message'=>'Update User Failed',
            'data'=>null
        ],400); //return if update failed
    }

    //Delete
    public function destroy($id)
    {
        $users = User::find($id); //find user by id

        if(is_null($users)){
            return response([
                'message'=>'User Not Found',
                'data'=>null
            ],400);
        } //return if user by id not found 

        if($users->delete()){
            return response([
                'message'=>'Delete User Success',
                'data'=>$users
            ],200);
        } //return if user has been deleted

        return response([
            'message'=>'Delete user Failed',
            'data'=>null
        ],400); //return if failed delete user
    }

    // public function logout() {
    //     $user = Auth::user()->token();
    //     $user->revoke();
    //     return response([
    //         'message' => 'Logout Success',
    //         'user' => $user,
    //     ]);
    // }
}
