<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BoardController extends Controller
{
    /**
     * ボード画面表示
     *
     * @return void
     */
    public function show()
    {
        $user = Auth::user();

        $posts = Post::select([
            'posts.*',
            'users.user_name',
            'files.file_name'
        ])
        ->join('users', 'posts.user_uuid', '=', 'users.uuid')
        ->leftJoin('files', 'posts.file_uuid', '=', 'files.uuid')
        ->orderByDesc('posts.created_at')
        ->limit(20)
        ->get();

        foreach ($posts as $post) {
            if ($post->file_uuid) {
                $extension = pathinfo($post->file_name, PATHINFO_EXTENSION);
                $post->file_url = Storage::disk('s3')->url('uploads/' . $post->file_uuid . '.' . $extension);
            } else {
                $post->file_url = null;
            }
        }

        return view('main.board', compact('user', 'posts'));
    }

    /**
     * 投稿処理
     *
     * @param Request $request
     * @return void
     */
    public function post(Request $request)
    {
        $user = Auth::user();

        $postUuid = Str::uuid()->toString();

        Post::create([
            'uuid' => $postUuid,
            'user_uuid' => $user->uuid,
            'content' => $request->content ?? '',
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $fileUuid = Str::uuid()->toString();

            $extension = pathinfo($request->file->getClientOriginalName(), PATHINFO_EXTENSION);

            $path = $request->file('file')->storeAs('uploads', $fileUuid . '.' . $extension, 's3'); 

            File::create([
                'uuid' => $fileUuid,
                'file_name' => $request->file->getClientOriginalName(),
            ]);

            Post::where('uuid', $postUuid)->update([
                'file_uuid' => $fileUuid,
            ]);
        }

        return redirect()->back();
    }
}