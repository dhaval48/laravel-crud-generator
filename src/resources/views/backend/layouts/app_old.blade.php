<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name'))</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  @yield('before-styles')
  <!-- Bootstrap 3.3.6 -->
  {{-- <link rel="stylesheet" href="{{ asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css') }}"> --}}
  <link rel="stylesheet" href="/css/bootstrap-3.4.0.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.5.95/css/materialdesignicons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css">

  @yield('after-styles')
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper" id="app">

  @include('crudpack::backend.includes.head')
  {{-- @include('crudpack::backend.includes.side') --}}
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @if(isset($data) && $data['dir'] == "setting")
      <section class="content-header">
          <h1>
              {{$data['lang']['list']}}
          </h1>
      </section>
    @endif
    <!-- Main content -->
    <section class="content">
      {{-- @include('backend.includes.messages') --}}
      @yield('content')
      <!-- Your Page Content Here -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  @include('crudpack::backend.includes.foot')
  {{-- @include('backend.includes.loader') --}}
</div>
<!-- ./wrapper -->
<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
{{-- <script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script> --}}
{{-- <script src="{{ asset('/js/jquery-3.4.0.min.js') }}"></script> --}}


<!-- AdminLTE App -->
<script src="/js/app.js"></script>

<script src="https://cdn.jsdelivr.net/npm/pretty-checkbox-vue@1.1/dist/pretty-checkbox-vue.min.js"></script>

@yield('after-scripts')

 <script type="text/javascript">
    function startLoader() {
        $("#loaderModal").modal('show');
    }
    function closeLoader() {
        $("#loaderModal").modal('hide');
    }

    startLoader();
    $(document).ready(function(){
      closeLoader();    
      
      // $( ".f" ).select();
      // $( ".f" ).focus();
    });
  </script>

</body>
</html>
