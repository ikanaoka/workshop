<!DOCTYPE html>
<html>
    @include('layouts.head')
    <body class="login_body">
        <form>
            <div class="login_form_container">
                <p class="login_title">アカウント作成</p>
                <p class="login_sub_title">ユーザー名</p>
                <input class="login_input" type="text">
                <p class="login_sub_title">パスワード</p>
                <input class="login_input" type="password">
                <p class="login_sub_title">パスワードの確認</p>
                <input class="login_input" type="password">
                <p></p>
                <input class="submit_button" type="submit" value="アカウントを作成">
            </div>
        </form>
    </body>
</html