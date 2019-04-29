<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ env('APP_NAME') }} | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('/bower_components/AdminLTE/dist/css/AdminLTE.min.css') }}">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.5.95/css/materialdesignicons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css">
  <style type="text/css">
    .top10{padding-top: 10px}
  </style>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>{{ env('APP_NAME') }}</b></a>
  </div>
  <!-- /.login-logo -->
  <div id="app">
    <login :module="{{json_encode($data)}}"></login>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script src="/js/app.js"></script>

<!-- jQuery 2.2.3 -->
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/pretty-checkbox-vue@1.1/dist/pretty-checkbox-vue.min.js"></script>
<script>
  
</script>
</body>
</html>