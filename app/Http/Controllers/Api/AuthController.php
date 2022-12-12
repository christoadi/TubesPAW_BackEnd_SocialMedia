<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
// use Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Exception;
use Carbon\Carbon;

class AuthController extends Controller
{
    //register
    public function register(Request $request){
        $registrationData = $request->all();
        $validate = Validator::make($registrationData, [
            'name' => 'required',
            'gender' => 'required',
            'tanggalLahir' => 'required|date_format:Y-m-d',
            'email' => 'required|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required'
        ]); //input validation

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); // return error validasi input

        // $temp = $registrationData['password'];
        $registrationData['password'] = bcrypt($request->password); // enkripsi password
        $user = User::create($registrationData); // membuat user baru

        event(new Registered($user));

        // try{
        //     $date = Carbon::now();
        //     $detail = [
        //         'email' => $registrationData['email'],
        //         'password' => $temp,
        //         'date' => $date,
        //     ];
        //     Mail::to($registrationData['email'])->send(new EmailVerification($detail));
        //     return response([
        //         'message' => 'Register Success and email send successfully',
        //         'user' => $user
        //     ], 200);
        // } catch(Exception $e) {
        //     return response([
        //             'message' => 'Register Success but cannot send the email',
        //             'user' => $user
        //         ], 200);
        //     }

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200); //return data user dalam bentuk json
    }
    

    //Login
    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]); // membuat rule validasi input

        if ($validate->fails()){
            return response(['message' => $validate->errors()], 400); // return error validasi input
        }    

        if (!Auth::attempt($loginData)){
            return response(['message' => 'Invalid Credentials'], 401); // return error gagal login
        }    

        /** @var \App\Models\User $user **/
        $user = Auth::User();
        if($user->email_verified_at!=null){
            $token = $user->createToken('Authentication Token')->accessToken; // generate token
        }else{
            return response(['message'=>'Email Not verified!'],401);
        }    
        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]); // return data user dan token dalam bentuk json
    }

    //show user
    public function show($id){
        $users = User::find($id); //mencari course berdasarkan data id

        if (!is_null($users)) {
            return response([
                'message' => 'Retrive User Success',
                'data' => $users
            ], 200);
        }

        return response([
            'message' => 'User Not Found',
            'data' => null
        ], 404);
    }

    //Update
    public function update(Request $request, $id){
        $users = User::find($id);
        if(is_null($users)){
            return response([
                'message'=>'User Not Found',
                'data'=>null
            ],400);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'name' => 'required|max:60',
            'gender' => 'required',
            'tanggalLahir' => 'required|date_format:Y-m-d',
            'username' => ['required',Rule::unique('users')->ignore($users)],
            'password' => ''
        ]); // validasi data

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        $users->name = $updateData['name'];
        $users->gender = $updateData['gender'];
        $users->tanggalLahir = $updateData['tanggalLahir'];
        $users->username = $updateData['username'];
        if ($updateData['password'] != ""){
            $users->password = bcrypt($updateData['password']);
        }

        if($users->save()){
            return response([
                'message'=> 'Update User Success',
                'data'=>$users
            ],200);
        }
        return response([
            'message'=>'Update User Failed',
            'data'=>null
        ],400);
    }

    //delete user
    public function destroy($id)
    {
        $users = User::find($id);

        if(is_null($users)){
            return response([
                'message'=>'User Not Found',
                'data'=>null
            ],400);
        }//return message saat database tidak ditemukan

        if($users->delete()){
            return response([
                'message'=>'Delete User Success',
                'data'=>$users
            ],200);
        }

        return response([
            'message'=>'Delete user Failed',
            'data'=>null
        ],400);
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
