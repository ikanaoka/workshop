<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\File;
use App\Models\Like;
use App\Models\Reply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ReplyController extends Controller
{
    /**
     * 返信画面表示
     *
     * @param Request $request
     * @return void
     */
    public function show(Request $request)
    {
        if ($request->post_uuid) {
            $user = Auth::user();

            $post = Post::select([
                'posts.*',
                'users.user_name',
                'files.file_name',
                DB::raw('COUNT(likes.uuid) as like_count')
            ])
            ->join('users', 'posts.user_uuid', '=', 'users.uuid')
            ->leftJoin('files', 'posts.file_uuid', '=', 'files.uuid')
            ->leftJoin('likes', 'posts.uuid', '=', 'likes.post_uuid')
            ->where('posts.uuid', $request->post_uuid)
            ->groupBy('posts.uuid')
            ->orderByDesc('posts.created_at')
            ->first();

            $like = Like::where('user_uuid', $user->uuid)
            ->where('post_uuid', $post->uuid)
            ->first();

            if ($like) {
                $post->liked_by_user = true;
            } else {
                $post->liked_by_user = false;
            }

            if ($post->file_uuid) {
                $extension = pathinfo($post->file_name, PATHINFO_EXTENSION);
                $post->file_url = Storage::disk('s3')->url('uploads/' . $post->file_uuid . '.' . $extension);
            } else {
                $post->file_url = null;
            }     
            
            $replies = Reply::select([
                'replies.*',
                'users.user_name',
                'files.file_name',
            ])
            ->join('users', 'replies.user_uuid', '=', 'users.uuid')
            ->leftJoin('files', 'replies.file_uuid', '=', 'files.uuid')
            ->where('replies.post_uuid', $post->uuid)
            ->orderByDesc('replies.created_at')
            ->get();

            foreach ($replies as $reply) {
                if ($reply->file_uuid) {
                    $extension = pathinfo($reply->file_name, PATHINFO_EXTENSION);
                    $reply->file_url = Storage::disk('s3')->url('uploads/' . $reply->file_uuid . '.' . $extension);
                } else {
                    $reply->file_url = null;
                }
            }

            return view('main.reply', compact('user', 'post', 'replies'));

        } else {
            return redirect()->route('home');
        }
    }

    /**
     * 返信処理
     *
     * @param Request $request
     * @return void
     */
    public function reply(Request $request)
    {
        $user = Auth::user();

        $replyUuid = Str::uuid()->toString();

        Reply::create([
            'uuid' => $replyUuid,
            'user_uuid' => $user->uuid,
            'post_uuid' => $request->post_uuid,
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

            Reply::where('uuid', $replyUuid)
            ->update([
                'file_uuid' => $fileUuid,
            ]);
        }

        return redirect()->back();
    }
}