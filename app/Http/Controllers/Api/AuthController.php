<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //Validacion de datos
        $validate= $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        // Alta de usuario
        $user = new User();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        // Respuesta
        return response()->json([
            "message" => "Método REGISTER ok"
        ]);
        //return response($user, Response::HTTP_CREATE);
    }
    public function login(Request $request){
        $credentials= $request->validate([
            'email'=>['required','email'],
            'password'=>['required']
        ]);

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token',$token,60*24);
            return response(["token"=>$token],Response::HTTP_OK)->withoutCookie($cookie);
        }else{
            return response(["message"=>"Credenciales incorrectas"],Response::HTTP_UNAUTHORIZED);
        }

    }

    public function userProfile(Request $request){
        return response()->json([
            "message"=>"userProfile OK",
            "userData"=>auth()->user()
        ],Response::HTTP_OK);
    }

    public function logout(){
        $cookie =  Cookie::forget('cookie_token');
        return response(["message"=>"Cierre de sesion OK"], Response::HTTP_OK);
    }

    public function allUsers(){
        $users = User::all();
        return response()->json([
            "users" => $users
        ]);
    }
}
