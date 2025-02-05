<!DOCTYPE html>
<html>
    @include('layouts.head')
    <body>
        <header>
            <button class="button liked_posts" id="main_posts">
                戻る
            </button>
            <span class="user_name">{{ $user->user_name }}</span>
            <button class="button" id="logout">ログアウト</button>
        </header>
        <div class="main_body">
            <form method="POST" action="{{ route('home.reply.post') }}" enctype="multipart/form-data">
            @csrf
                <div class="post_container">
                    <input type="hidden" name="post_uuid" value="{{ $post->uuid }}">
                    <textarea class="post_content" type="text" name="content" placeholder="{!! $post->user_name !!}さんの投稿に返信しましょう！"></textarea>
                    <div class="post_button_container">
                        <div class="photo_container">
                            <image class="camera_icon" id="attach" src="{{ asset('images/camera-fill.svg') }}">
                            <span class="photo_name"></span>
                        </div>
                        <input type="file" name="file" id="file-input" accept="image/*" style="display: none;">
                        <button class="submit_button no_margin" id="post">
                            <image class="small_icon" src="{{ asset('images/send.svg') }}">
                            返信する
                        </button>
                    </div>
                </div>
            </form>
            <div class="posted_list_container">
                <div class="posted_item_container">
                    <div class="posted_title_container">
                        <span class="posted_user_name">{{ $post->user_name }}</span>
                        <span class="posted_time">{{ $post->created_at }}</span>
                        <img class="heart_icon" src="{{ asset('images/arrow-through-heart.svg') }}" data-post-uuid="{{ $post->uuid }}" data-user-uuid="{{ $user->uuid }}" @if($post->liked_by_user) style="display: none;" @endif>
                        <img class="heart_icon_red" src="{{ asset('images/arrow-through-heart-fill.svg') }}" data-post-uuid="{{ $post->uuid }}" data-user-uuid="{{ $user->uuid }}" @if(!$post->liked_by_user) style="display: none;" @endif>
                        <span class="like_count">{{ $post->like_count }}</span>
                    </div>
                    <div class="posted_content">{!! nl2br(e($post->content)) !!}</div>
                    @if (!is_null($post->file_url))
                        <div class="posted_photo_container"> 
                            <img class="posted_photo" src="{{ $post->file_url }}">
                        </div>
                    @endif
                </div>
            </div>
            @if ($replies->isNotEmpty())
                <span class="reply_count">{{ $replies->count() }}件の返信</span>
                <div class="posted_list_container">
                    @foreach ($replies as $reply)
                        <div class="posted_item_container">
                            <div class="posted_title_container">
                                <img class="reply_black_icon" src="{{ asset('images/arrow-return-right-black.svg') }}">
                                <span class="posted_user_name">{{ $reply->user_name }}</span>
                                <span class="posted_time">{{ $reply->created_at }}</span>
                            </div>
                            <div class="posted_content">{!! nl2br(e($reply->content)) !!}</div>
                            @if (!is_null($reply->file_url))
                                <div class="posted_photo_container"> 
                                    <img class="posted_photo" src="{{ $reply->file_url }}">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </body>
</html>

<script>
    $('#logout').hover(
        function (){
            $(this).addClass('hovered');
        },
        function (){
            $(this).removeClass('hovered');
        }
    );

    $('#logout').on('click', function (){
        window.location.href = 'login';
    });

    $('#attach').on('click', function (){
        $('input[type=file]').trigger('click');
    });

    $('#file-input').change(function (){
        var file = this.files[0];
        var fileName = file.name;
        $('.photo_name').text(fileName);
    });

    $('#post').on('click', function (){
        $('form').submit();
    });

    $('.heart_icon').on('click', function () {
        var postUuid = $(this).data('post-uuid');
        var userUuid = $(this).data('user-uuid');
        var heartIcon = $(this); 
        var likeCount = heartIcon.siblings('.like_count');

        $.ajax({
            url: '/home/like',
            type: 'POST',
            data: {
                post_uuid: postUuid,
                user_uuid: userUuid,
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                heartIcon.hide();
                heartIcon.siblings('.heart_icon_red').show();

                var currentLikeCount = parseInt(likeCount.text());
                likeCount.text(currentLikeCount + 1); 
            },
            error: function() {
                alert('いいねの追加に失敗しました。');
            }
        });
    });

    $('.heart_icon_red').on('click', function () {
        var postUuid = $(this).data('post-uuid');
        var userUuid = $(this).data('user-uuid');
        var heartIcon = $(this);
        var likeCount = heartIcon.siblings('.like_count');
        
        $.ajax({
            url: '/home/like',
            type: 'DELETE',
            data: {
                post_uuid: postUuid,
                user_uuid: userUuid,
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                heartIcon.hide();
                heartIcon.siblings('.heart_icon').show();

                var currentLikeCount = parseInt(likeCount.text());
                likeCount.text(currentLikeCount - 1); 
            },
            error: function() {
                alert('いいねの削除に失敗しました。');
            }
        });
    });

    $('#main_posts').on('click', function (){
        window.location.href = '/home';
    });
</script>