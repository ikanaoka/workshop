<!DOCTYPE html>
<html>
    @include('layouts.head')
    <body>
        <header>
            <span class="user_name">{{ $user->user_name }}</span>
            <button class="button" id="logout">ログアウト</button>
        </header>
        <div class="main_body">
            <form method="POST" action="{{ route('home.post') }}" enctype="multipart/form-data">
            @csrf
                <div class="post_container">
                    <textarea class="post_content" type="text" name="content" placeholder="{!! $user->user_name !!}さん、お元気ですか？"></textarea>
                    <div class="post_button_container">
                        <div class="photo_container">
                            <image class="camera_icon" id="attach" src="{{ asset('images/camera-fill.svg') }}">
                            <span class="photo_name"></span>
                        </div>
                        <input type="file" name="file" id="file-input" accept="image/*" style="display: none;">
                        <button class="submit_button no_margin" id="post">
                            <image class="small_icon" src="{{ asset('images/send.svg') }}">
                            投稿する
                        </button>
                    </div>
                </div>
            </form>
            <div class="posted_list_container">
                @if (!empty($posts))
                    @foreach($posts as $post)
                    <div class="posted_item_container">
                        <div class="posted_title_container">
                            <span class="posted_user_name">{{ $post->user_name }}</span>
                            <span class="posted_time">{{ $post->created_at }}</span>
                        </div>
                        <div class="posted_content">{!! nl2br(e($post->content)) !!}</div>
                        @if (!is_null($post->file_url))
                            <div class="posted_photo_container"> 
                                <img class="posted_photo" src="{{ $post->file_url }}">
                            </div>
                        @endif
                    </div>
                    @endforeach
                @endif
            </div>
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
</script>