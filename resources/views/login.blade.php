@extends('layouts.empty')

@section('css')
  <link rel="stylesheet" href="/public/default/css/iCheck/square/blue.css">
@endsection

@section('js')
<script src="/public/default/js/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
@endsection

@section('content')
<div class="login-box">
  <div class="login-logo">
    <b>匠人课程系统</b>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">请登录管理员账号</p>

    <form action="/login" method="post">
    {{ csrf_field() }}
      <div class="form-group has-feedback">
        <input type="text" name="user" class="form-control" placeholder="用户名">
        <span class="glyphicon glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="密码">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
@endsection
