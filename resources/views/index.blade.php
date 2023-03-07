<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>首页</title>

    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu"
        crossorigin="anonymous"
    />

    <!-- 可选的 Bootstrap 主题文件（一般不用引入） -->
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css"
        integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ"
        crossorigin="anonymous"
    />

    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script
        src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd"
        crossorigin="anonymous"
    ></script>
</head>
<body>
<div class="leader">
    <ul class="nav nav-pills">
        <li role="presentation" class="active"><a href="/">Home</a></li>
        <li role="presentation"><a href="/news">新闻</a></li>
        <li role="presentation"><a href="/product">产品</a></li>
    </ul>
</div>

<div class="content">
    Hello, {{ $name }}.
</div>
<h2>The current UNIX timestamp is {{ time() }}.</h2>
<h3>Hello, {!! $name !!}.</h3>
<h5>由于许多 JavaScript 框架也使用「花括号」来标识将显示在浏览器中的表达式,可以使用 @ 符号来表示 Blade 渲染引擎应当保持不变</h5>
Hello, @{{ name }}.
<div>
@if (count($logs) === 1)
    // 有一条记录
@elseif (count($logs) > 1)
    // 有多条记录
@else
    // 没有记录
@endif
</div>
</body>
</html>
