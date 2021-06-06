<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    private function checkAdmin() {
        $user = User::select()->where('role' , '=', 1)->first();
        
        if($user != null) {
            return true;
        } else {
            return false;
        };
    }
    
    public function register(Request $request)
    {
        // Validación
        $validation =  Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'passwordcheck' => 'required|string|same:password',
            'role' => 'nullable|boolean',
        ]);        

        if ($validation->fails()) {
            $userExists = User::select()->where('email', '=', $request->email)->first();
            if($userExists != null) {
                return response()->json('User already exists', 409);
            } else {
                return response()->json($validation->errors(), 400);
            };            
        } else {
            if ($this->checkAdmin() && $request->role == 1) {
                return response()->json('Admin already exists', 403);
            } else {                
               
                $allData = $request->all();
                // Encriptamos la contraseña
                $allData['password'] = bcrypt($allData['password']);
                // Asignamos el modelo de usuario con los datos
                $user = User::create($allData);

                $resArr = [
                    'message' => 'Succesfully created user!',
                    'token' => $user->createToken('api-application')->accessToken,
                    'data' => $user->toArray()
                ];          

                return response()->json($resArr, 201);              
            };            
        };
    }


    public function login(Request $request)
    {
        // Validación
        $validation =  Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        } else {
            
            if (Auth::attempt ([
                'email' => $request->email,
                'password' => $request->password,
            ])) {
                $user = Auth::user();
                
                $resArr = [
                    'message' => 'Succesfully login user!',
                    'token' => $user->createToken('api-application')->accessToken,
                    'data' => $user->toArray()                    
                ];  

                return response()->json($resArr, 200);  
            } else {
                return response()->json(['error' => 'Unauthorized user'], 401);  
            };
        };    
        
    }

    public function logout(Request $request) {
        $user = $request->user();
        $accessToken = $request->user()->token();
        $request->user()->token()->revoke();        
        $resArr = [
            'message' => 'Succesfully logout user!',
            'token' => $accessToken,
            'data' => $user->toArray()
        ]; 
        return response()->json($resArr, 200);      
    
    }

    public function getUserData(Request $request) {
        $user = $request->user();            
        $resArr = [
            'message' => 'User Data',
            'data' => $user->toArray()
        ]; 
        return response()->json($resArr, 200);
    }

}
