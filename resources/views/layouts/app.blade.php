
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>@yield('title','Laravel Shop') - Laravel 电商教程</title>
    <!-- 样式 -->
    <link href="{{mix('css/app.css')}}" rel="stylesheet">
</head>
<body>
    {{-- 此行注释为Blade注释，不会返回到html中 --}}
    {{-- route_class()是自定义的辅助方法 --}}
    <div id="app" class="{{route_class()}}-page">
        {{-- @include()包含子视图（如：layouts/_header.blade.php) --}}
        @include('layouts._header')
        <div class="container">
            @yield('content')
        </div>
        @include('layouts._footer')
    </div>
    <!-- JS脚本 -->
    <script src="{{mix('js/app.js')}}"></script>
    @yield('scriptsAfterJs')
</body>
</html>