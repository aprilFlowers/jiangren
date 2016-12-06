<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>犀牛互动CMS</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/public/default/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/public/default/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/public/default/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/public/default/css/AdminLTE.min.css">
  @yield('css')

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="/public/default/css/html5shiv.min.js"></script>
  <script src="/public/default/css/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">

@yield('content')

<!-- jQuery 2.2.0 -->
<script src="/public/default/js/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/public/default/js/bootstrap.min.js"></script>
@yield('js')
</body>
</html>
