<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\File;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
            'files.file_name',
            DB::raw('COUNT(likes.uuid) as like_count'),
            DB::raw('COUNT(replies.uuid) as reply_count')
        ])
        ->join('users', 'posts.user_uuid', '=', 'users.uuid')
        ->leftJoin('files', 'posts.file_uuid', '=', 'files.uuid')
        ->leftJoin('likes', 'posts.uuid', '=', 'likes.post_uuid')
        ->leftJoin('replies', 'posts.uuid', '=', 'replies.post_uuid')
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

        return view('main.board', compact('user', 'posts', 'likes'));
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

    /**
     * いいね登録処理
     *
     * @param Request $request
     * @return void
     */
    public function like(Request $request)
    {
        $user = Auth::user();

        $post = Post::where('uuid', $request->post_uuid)->first();

        $like = Like::where('user_uuid', $user->uuid)->where('post_uuid', $post->uuid)->first();

        if (!$like) {
            Like::create([
                'uuid' => Str::uuid()->toString(),
                'user_uuid' => $user->uuid,
                'post_uuid' => $post->uuid,
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * いいね削除処理
     *
     * @param Request $request
     * @return void
     */
    public function dislike(Request $request)
    {
        $user = Auth::user();

        $post = Post::where('uuid', $request->post_uuid)->first();

        $like = Like::where('user_uuid', $user->uuid)->where('post_uuid', $post->uuid)->first();

        if($like) {
            $like->delete();
        }

        return response()->json(['success' => true]);
    }
}