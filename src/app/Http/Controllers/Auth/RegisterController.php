<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * アカウント登録画面表示
     *
     * @return void
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * アカウント登録処理
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {
        //バリデーション
        $request->validate([
            'user_name' => 'required|string|max:255|unique:users,user_name',  
            'password' => 'required|string|confirmed',            
        ]);

        //ユーザー登録
        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'last_login_at' => null
        ]);

        return redirect()->route('login');
    }
}