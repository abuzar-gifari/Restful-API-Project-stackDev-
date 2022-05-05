<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class APIController extends Controller
{
    public function getUsers($id=null){
        if (empty($id)) {
            $getUsers=User::all();
            // return $getUsers;
            return response()->json([ "users"=>$getUsers ],200);    
        }else {
            $getUsers=User::find($id);
            return response()->json([ "users"=>$getUsers ],200);
        }
    }

    public function addUsers(Request $request){
        if ($request->isMethod('post')) {
            // $user_data= $request->input();
            // return $user_data;
            $userData = $request->input();

            /*
            // CHECK THAT IF THESE FIELDS ARE EMPTY!!
            if ( empty($request["name"]) || empty($request["email"]) || empty($request["password"]) ) {
                return response()->json([ "status"=>false,"message"=>"please enter complete details" ],422);
            }

            // SIMPLE EMAIL VALIDATION
            if (!filter_var($request["email"], FILTER_VALIDATE_EMAIL)) {
                return response()->json([ 
                    "status"=>false,
                    "message"=>"enter valid email address" 
                ],422);    
            }

            // CHECK THAT IS EMAIL IS EXISTS OR NOT!!
            $userCount = User::where('email',$userData["email"])->count();
            if ($userCount>0) {
                return response()->json([ 
                    "status"=>false,
                    "message"=>"Email Already Exists!!" 
                ],422);
            }
            
            */

            // ADVANCE VALIDATION PROCESS   
            $rules=[
                "name"=>"required",
                "email"=>"required|email|unique:users",
                "password"=>"required",
            ];

            $customMessages=[
                "name.required"=>"your name is required",
                "email.required"=>"your email is required",
                "email.email"=>"your email is not valid",
                "email.unique"=>"email already exists",
                "password.required"=>"password is required",
            ];

            $validator = FacadesValidator::make($userData,$rules,$customMessages);

            if ($validator->fails()) {
                return response()->json([ "success"=>false,"message"=>$validator->errors() ],422);    
            }

            $user = new User();
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->save();
            return response()->json([ "message"=>"user created successfully" ],201);
        }
    }

    public function addMultipleUsers(Request $request){
        if ($request->isMethod('post')) {
            $userData = $request->input();
            // echo "<pre>";
            // print_r($userData);
            // die;

            $rules=[
                "users.*.name"=>"required",
                "users.*.email"=>"required|email|unique:users",
                "users.*.password"=>"required",
            ];

            $customMessages=[
                "users.*.name.required"=>"your name is required",
                "users.*.email.required"=>"your email is required",
                "users.*.email.email"=>"your email is not valid",
                "users.*.email.unique"=>"email already exists",
                "users.*.password.required"=>"password is required",
            ];

            $validator = FacadesValidator::make($userData,$rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(),422);    
            }

            foreach($userData['users'] as $value){
                $user = new User();
                $user->name = $value['name'];
                $user->email = $value['email'];
                $user->password = bcrypt($value['password']);
                $user->save();
            }            
            return response()->json([ "message"=>"Multiple User Created Successfully" ],201);
        }        
    }

    public function UpdateUserDetails(Request $request,$id=null){
        if ($request->isMethod('put')) {
            $userData = $request->input();
            User::where('id',$id)->update([
                "name"=>$userData['name'],
                "email"=>$userData['email'],
                "password"=>$userData['password'],
            ]);
            return response()->json([ "message"=>"user information updated successfully" ],202);
        }        
    }

    public function UpdateUserName(Request $request,$id){
        if ($request->isMethod('patch')) {
            $userData = $request->input();
            User::where('id',$id)->update([
                "name"=>$userData['name'],
            ]);
            return response()->json([ "message"=>"user name updated successfully" ],202);
        }        
    }

    
    public function DeleteUser($id){
        User::where('id',$id)->delete();
        return response()->json([ "message"=>"user deleted successfully" ],202);    
    }

    public function getUsersList(Request $request){
        $header=$request->header('Authorization');
        if (empty($header)) {
            $message="Header Authorization part is missing";
            return response()->json([ "message"=>$message ],422);
        }else {
            if ($header == "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFidXphciBHaWZhcmkiLCJpYXQiOjE1MTYyMzkwMjJ9.r4vCuN7pdA0vC6YMUokA7C6742h8s_zn2SiXCggdeac") {
                $users=User::get();
                return response()->json([ "users"=>$users ],202);    
            }else {
                $message="Header Authorization part is incorrect";
                return response()->json([ "status"=>false,"message"=>$message ],422);
            }
                
        }
    }

    public function RegisterUser(Request $request){
        if ($request->isMethod('post')) {
            $userData = $request->input();
            $apiToken = Str::random(60);

            $user = new User();
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->api_token = $apiToken;
            $user->save();
            return response()->json([
                "status"=>true, 
                "message"=>"user registered successfully",
                "token"=>$apiToken 
            ],201);
        }
    }

    public function LoginUser(Request $request){
        if ($request->isMethod('post')) {
            $userData = $request->input();
            $apiToken = Str::random(60);

            // ADVANCE VALIDATION PROCESS   
            $rules=[
                "email"=>"required|email|exists:users",
                "password"=>"required",
            ];

            $customMessages=[
                "name.required"=>"your name is required",
                "email.required"=>"your email is required",
                "email.email"=>"your email is not valid",
                "email.exists"=>"email already exists",
                "password.required"=>"password is required",
            ];

            $validator = FacadesValidator::make($userData,$rules,$customMessages);

            if ($validator->fails()) {
                return response()->json([ "success"=>false,"message"=>$validator->errors() ],422);    
            }

            $userDetails=User::where('email',$userData['email'])->first();

            if (password_verify($userData['password'],$userDetails->password)) {
                $apiToken=Str::random(60);
                User::where('email',$userData['email'])->update(['api_token'=>$apiToken],200);
                return response()->json([ "success"=>true,"message"=>"User Login Success" ],200);
            }else {
                return response()->json([ "success"=>false,"message"=>"password is incorrect" ],422);    
            }
        }
    }

    public function LogoutUser(Request $request){
        $api_token=$request->header('Authorization');
        if (empty($api_token)) {
            $message="User token is missing in api header";
            return response()->json([ "message"=>$message ],422);
        }else {
            $api_token = str_replace("Bearer ","",$api_token);
            $userCount = User::where('api_token',$api_token)->count();
            if ($userCount>0) {
                User::where('api_token',$api_token)->update(['api_token'=>null]);
                return response()->json([ "success"=>true,"message"=>"User Logout Successfully" ],200);
            }              
        }        
    }
} 
