<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * ログイン画面表示
     *
     * @return void
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     *
     * @param Request $request
     * @return void
     */
    public function enter(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required',
        ], [], [
            'user_name' => 'ユーザー名',   
            'password' => 'パスワード',     
        ]);

        $user = User::where('user_name', $request->user_name)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('home');
        } else {
            return back()->withErrors(['message' => '認証に失敗しました。']);
        }
    }
}