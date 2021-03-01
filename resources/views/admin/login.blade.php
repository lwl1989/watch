<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="keywords" content="TTPush,踢一下">
    <meta name="description" content="TTPush管理平台TTPush APP的管理後台" />
    <title>TTPush-管理平台</title>
    <link rel="stylesheet" href="{{ mix('css/login.css') }}">
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
</head>

<body>
<div id="app">
    <admin-component>
    </admin-component>
</div>
<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vue.js') }}"></script>
<script src="{{ mix('js/element-ui.js') }}"></script>
<script src="{{ mix('js/auth.js') }}"></script>
</body>
</html>
