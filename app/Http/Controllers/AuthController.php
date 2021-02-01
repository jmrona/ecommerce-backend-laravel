<?php

namespace App\Http\Controllers;

use App\Mail\RecoverPassword;
use App\Mail\resetPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'lastname' => 'required',
            'email' => ' required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Bad request'
            ]);
        }

        $user = new User;
        $user->name = e($request->name);
        $user->lastname = e($request->lastname);
        $user->email = e($request->email);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'User registered succesfully',
            'user' => $user,
        ]);

    }

    public function login( Request $request ){
        $validator = Validator::make($request->all(),[
            'email' => ' required|email',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => 'Email or password invalid'
            ]);
        }

        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials, $remember = true)){
            return response()->json([
                'status' => 500,
                'msg' => 'Credencential doesn\'t exist'
            ]);
        }

        $user = User::where('email',$request->email)->first();

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg'=> 'Logged successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout( Request $request ){
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Token deleted successfully'
        ]);
    }

    public function renew( Request $request ){
        if(Auth::check()){
            $request->user()->tokens()->delete();
            $newToken = $request->user()->createToken('authToken')->plainTextToken;

            return response()->json([
                'status' => 200,
                'ok' => true,
                'msg'=> 'Token updated successfully',
                'user' => $request->user(),
                'token' => $newToken
            ]);
        }

        return response()->json([
            'status' => 400,
            'ok' => false,
            'msg'=> 'User no authenticated'
        ]);
    }

    public function recoverPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => ' required|email'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Please, type a valid email'
            ]);
        }

        $user = User::where('email', trim(($request->email)))->first();
        if(!$user){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Please, type a valid email'
            ]);
        }

        $code = rand(100000, 999999);
        $data = [
            'name' => $user->firstname,
            'email' => $user->email,
            'code' => $code,
        ];
        $user->password_code = $code;

        if($user->save()){
            Mail::to($user->email)->send(new RecoverPassword($data));

            return response()->json([
                'status' => 200,
                'ok' => true,
                'msg' => 'Please check your email, we sent a email with a code'
            ]);
        }

        return response()->json([
            'status' => 400,
            'ok' => false,
            'msg' => 'We didn\'t find this email'
        ]);
    }

    public function resetPassword(Request $request){
        $user = User::where('email', trim($request->email))
            ->where('password_code', $request->code)
            ->first();

        if(!$user->password_code === $request->code){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'This code is not valid'
            ]);
        }

        $user->password_code = null;
        $user->password = Hash::make($request->newPassword);

        if($user->save()){
            $data = [
                'name' => $user->name,
                'new_password' => $request->newPassword,
            ];
            Mail::to($user->email)->send(new resetPassword($data));

            return response()->json([
                'status' => 200,
                'ok' => true,
                'msg' => 'Password updated. You can log in with your new password now'
            ]);
        }

        return response()->json([
            'status' => 400,
            'ok' => true,
            'msg' => 'Something was wrong, please contact admin'
        ]);
    }
}
