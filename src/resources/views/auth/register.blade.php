<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ env('APP_NAME') }} | Sign up</title>
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

  <style type="text/css">
    .process_group_div{
        padding-bottom: 10px;
        border: 1px solid aliceblue;
        padding: 10px;
        margin-bottom: 10px;
    }
    .module {
        margin-bottom: 5px;
    }
    .register-box {
        margin: auto;
        width: 100%
    }
    .field-required{
        display: -webkit-inline-box;
        color: palevioletred;
        margin-left: 2px;
    }
    
</style>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
    <div class="row" id="app"> 
        <div class="col-md-offset-2 col-md-8">
            <div class="register-box">
                <div class="login-logo">
                    <a href="/login"><b>{{ env('APP_NAME') }}</b></a>
                </div>
              
                <div class="login-box-body">
                    <p class="login-box-msg">Sign up to start your session</p>
                    @include('backend.includes.messages')    

                    <form class="form-horizontal" role="form" method="POST" 
                    action="{{ route('register') }}" enctype="multipart/form-data" >
                    {{ csrf_field() }}
                                
                        <div class="form-group {{ form_error($errors, 'name')['error_class'] }}">
                            <div class="col-md-6 col-md-offset-3">

                                <label for="name" class="control-label">Name</label>
                                <div class="field-required">(Required)</div>               
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>
                            </div>
                        </div>

                        <div class="form-group {{ form_error($errors, 'email')['error_class'] }}">

                            <div class="col-md-6 col-md-offset-3">
                                <label for="email" class="control-label">E-Mail Address</label>
                                <div class="field-required">(Required)</div>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="form-group {{ form_error($errors, 'password')['error_class'] }}">
                            <div class="col-md-6 col-md-offset-3">
                                
                                <label for="password" class="control-label">Password</label>
                                <div class="field-required">(Required)</div>
                                <input id="password" type="password" class="form-control" name="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">    
                                <label for="password-confirm" class="control-label">Confirm Password</label>

                            
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-flat btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="/js/app.js"></script>

<!-- jQuery 2.2.3 -->
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js') }}"></script>

</body>
</html>
