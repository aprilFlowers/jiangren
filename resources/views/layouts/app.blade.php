<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <title>匠人课程系统</title>
  <link rel="stylesheet" href="/public/default/css/reset.min.css">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/public/default/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/public/default/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/public/default/css/ionicons.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/public/default/css/jquery-jvectormap-1.2.2.css">
  <link rel="stylesheet" href="/public/default/css/select2.min.css">
  <!-- fullCalendar 2.2.5-->
  <link rel="stylesheet" href="/public/default/css/fullcalendar.min.css">
  <link rel="stylesheet" href="/public/default/css/fullcalendar.print.css" media="print">
  <!-- Theme style -->
  <link rel="stylesheet" href="/public/default/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/public/default/css/_all-skins.min.css">
  <link rel="stylesheet" href="/public/default/css/admin.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="/public/default/css/html5shiv.min.js"></script>
  <script src="/public/default/css/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">

    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>匠人</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>匠人课程系统</b></span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="/public/default/img/user2-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs">{{$globalUser}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="/public/default/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                <p>
                  {{$globalUser}} - 匠人课程系统
                  <small>Member since 2016-06</small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-public/default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="/logout" class="btn btn-public/default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>

    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="/public/default/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{$globalUser}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> 在线</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        @foreach ($globalNav as $navName => $nav)
          @if(\Entrust::can($nav['perm']))
          <li class="treeview {{!empty($globalBreadcrumb[0]) && $navName == $globalBreadcrumb[0]['name'] ? 'active' : ''}}">
            <a href="{{!empty($nav['children']) ? '#' : $nav['url']}}">
              <i class="fa {{$nav['icon'] or 'fa-dashboard'}}"></i> <span>{{$navName}}</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            @if (!empty($nav['children']))
              <ul class="treeview-menu">
                @foreach ($nav['children'] as $cName => $c)
                  @if(\Entrust::can($c['perm']))
                  <li class="{{!empty($globalBreadcrumb[1]) && $cName == $globalBreadcrumb[1]['name'] ? 'active' : ''}}"><a href="{{$c['url']}}"><i class="fa {{$c['icon'] or 'fa-circle-o'}}"></i> {{$cName}}</a></li>
                  @endif
                @endforeach
              </ul>
            @endif
          </li>
          @endif
        @endforeach
        <li class="header">LABELS</li>
        <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>重要</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>提醒</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>信息</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{$globalBreadcrumb[0]['name'] or '控制面板'}}
        <small>{{$globalBreadcrumb[1]['name'] or ''}}</small>
      </h1>
      <ol class="breadcrumb">
        @foreach ($globalBreadcrumb as $i => $bread)
          <li>
            @if ($i == 0)
              <i class="fa fa-dashboard"></i>
            @endif
            <a href="{{!empty($bread['url']) ? $bread['url'] : '#'}}">{{$bread['name']}}</a>
          </li>
        @endforeach
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    @yield('content')
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.3.3
    </div>
    <strong>Copyright &copy; 2014-2015 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
    reserved.
  </footer>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.0 -->
<script src="/public/default/js/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/public/default/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="/public/default/js/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/public/default/js/app.min.js"></script>
<!-- Sparkline -->
<script src="/public/default/js/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="/public/default/js/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/public/default/js/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll 1.3.0 -->
<script src="/public/default/js/jquery.slimscroll.min.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="/public/default/js/Chart.min.js"></script>
<script src="/public/default/js/select2.full.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{--<script src="/public/default/js/dashboard2.js"></script>--}}
<!-- AdminLTE for demo purposes -->
<script src="/public/default/js/demo.js"></script>
<script src="/public/default/js/app.js"></script>
@yield('js')
</body>
</html>
