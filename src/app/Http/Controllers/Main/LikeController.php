<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\File;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    /**
     * お気に入り画面表示
     *
     * @return void
     */
    public function show()
    {
        $user = Auth::user();

        $posts = Post::select([
            'posts.*',
            'users.user_name',
            'files.file_name',
            DB::raw('COUNT(likes.uuid) as like_count'),
            DB::raw('COUNT(replies.uuid) as reply_count')
        ])
        ->join('users', 'posts.user_uuid', '=', 'users.uuid')
        ->leftJoin('files', 'posts.file_uuid', '=', 'files.uuid')
        ->leftJoin('likes', 'posts.uuid', '=', 'likes.post_uuid')
        ->leftJoin('replies', 'posts.uuid', '=', 'replies.post_uuid')
        ->where('likes.user_uuid', $user->uuid)
        ->groupBy('posts.uuid')
        ->orderByDesc('posts.created_at')
        ->limit(20)
        ->get();

        $likes = Like::where('user_uuid', $user->uuid)->get()->keyBy('post_uuid');

        foreach ($posts as $post) { 
            $post->liked_by_user = isset($likes[$post->uuid]);
                       
            if ($post->file_uuid) {
                $extension = pathinfo($post->file_name, PATHINFO_EXTENSION);
                $post->file_url = Storage::disk('s3')->url('uploads/' . $post->file_uuid . '.' . $extension);
            } else {
                $post->file_url = null;
            }
        }

        return view('main.like', compact('user', 'posts'));
    }
}