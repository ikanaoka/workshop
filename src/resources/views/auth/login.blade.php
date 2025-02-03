<!DOCTYPE html>
<html>
    @include('layouts.head')
    <body class="login_body">
        <form method="POST" action="{{ route('login.enter') }}">
        @csrf
            <div class="login_form_container">
                <p class="login_title">ログイン</p>
                @if ($errors->has('message'))
                    <p class="error">{{ $errors->first('message') }}</p>
                @endif
                <div class="input_title">
                    <p class="login_sub_title">ユーザー名</p>
                    @if ($errors->has('user_name'))
                        <p class="error">{{ $errors->first('user_name') }}</p>
                    @endif
                </div>
                <input class="login_input" type="text" name="user_name">
                <div class="input_title">
                    <p class="login_sub_title">パスワード</p>
                    @if ($errors->has('password'))
                        <p class="error">{{ $errors->first('password') }}</p>
                    @endif
                </div>
                <input class="login_input" type="password" name="password">
                <p></p>
                <input class="submit_button" type="submit" value="ログイン">
                <p class="login_white_title">───── または ─────</p>
                <a class="login_href" href="{{ route('register') }}">
                    <p class="login_sub_title" style="text-align: center;">アカウントを新規作成</p>
                </a>
            </div>
        </form>
    </body>
</html>