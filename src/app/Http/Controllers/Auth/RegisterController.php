<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        //バリデーション
        $request->validate([
            'user_name' => 'required|string|max:255|unique:users,user_name',  
            'password' => 'required|string',            
        ]);

        //ユーザー登録
        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('auth.login');
    }
}