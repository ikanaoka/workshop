<!DOCTYPE html>
<html>
    @include('layouts.head')
    <body class="login_body">
        <form>
            <div class="login_form_container">
                <p class="login_title">ログイン</p>
                <p class="login_sub_title">ユーザー名</p>
                <input class="login_input" type="text">
                <p class="login_sub_title">パスワード</p>
                <input class="login_input" type="password">
                <p></p>
                <input class="submit_button" type="submit" value="ログイン">
                <p class="login_white_title">───────── または ─────────</p>
                <a class="login_href" href="{{ route('auth.register') }}">
                    <p class="login_sub_title" style="text-align: center;">アカウントを新規作成</p>
                </a>
            </div>
        </form>
    </body>
</html>