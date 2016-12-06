@extends('layouts.app')

@section('js')
  <script>
    $(function(){
      $('#myForm').submit(function(){
        if($('#password').val() != $('#confirm').val()){
          alert('请确认密码！');
          return false;
        }
        return true;
      });
    });
  </script>
@endsection

@section('content')
  <div class="box box-primary">
    <!-- form start -->
    <form id="myForm" role="form" method="post" action="{{$controlUrl}}/update">
      {{ csrf_field() }}
      <input type="hidden" name="id" value="{{!empty($id) ? $id : ''}}">
      <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
      <div class="box-body">
        <div class="form-group">
          <label for="userLabel">新密码</label>
          <input type="password" class="form-control" id="password" placeholder="新密码" name="password">
        </div>
        <div class="form-group">
          <label for="userLabel">确认密码</label>
          <input type="password" class="form-control" id="confirm" placeholder="确认密码" name="confirm">
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <button type="submit" class="btn btn-primary">提交</button>
      </div>
    </form>
  </div>
@endsection
