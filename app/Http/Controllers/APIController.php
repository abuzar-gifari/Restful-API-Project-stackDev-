<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;

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
}
