<h1>登录成功</h1>
<ul>
    <li>登录方式: {{ $login_type }}</li>
    <li>id: {{ $id }}</li>
    <li>用户名: {{ $nickname }}</li>
    <li>头像: <img src="{{ $avatar_url }}" /> </li>
</ul>

<a href="/logout">退出</a>