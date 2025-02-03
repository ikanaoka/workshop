<!DOCTYPE html>
<html>
    @include('layouts.head')
    <body class="login_body">
        <form method="POST" action="{{ route('register.create') }}">
        @csrf
            <div class="login_form_container">
                <p class="login_title">アカウント作成</p>
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
                <div class="input_title">
                    <p class="login_sub_title">パスワードの確認</p>
                    @if ($errors->has('password_confirmation'))
                        <p class="error">{{ $errors->first('password_confirmation') }}</p>
                    @endif
                </div>
                <input class="login_input" type="password" name="password_confirmation">
                <p></p>
                <input class="submit_button" type="submit" value="アカウントを作成">
            </div>
        </form>
    </body>
</html