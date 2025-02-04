<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Main\BoardController;
use App\Http\Controllers\Main\LikeController;

Route::get('/', function () {
    return view('welcome');
});

//ログイン画面
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login/enter', [LoginController::class, 'enter'])->name('login.enter');

//アカウント作成画面
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register/create', [RegisterController::class, 'register'])->name('register.create');

//ユーザーがログインしていない場合にログイン画面へリダイレクトをする
Route::middleware(['auth'])->group(function () {
    //ボード画面
    Route::get('/home', [BoardController::class, 'show'])->name('home');
    Route::post('/home/post', [BoardController::class, 'post'])->name('home.post');
    Route::post('/home/like', [BoardController::class, 'like']);
    Route::delete('/home/like', [BoardController::class, 'dislike']);

    //お気に入り画面
    Route::get('/home/like/show', [LikeController::class, 'show'])->name('home.like.show');
});


