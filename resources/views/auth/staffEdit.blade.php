@extends('layouts.app')

@section('js')
  <script src="/public/default/js/ckeditor/ckeditor.js"></script>
  <script>
    $(function(){
      $('#myForm').submit(function(){
      if($('#password').val() != $('#confirm').val()){
        alert('请确认密码！');
        return false;
      }
      return true;
    });
      $(".select2").select2();
    });
  </script>
@endsection

@section('content')
  <div class="box box-primary">
    <!-- form start -->
    <form id="myForm" role="form" method="post" action="{{$controlUrl}}/update">
      {{ csrf_field() }}
      <input type="hidden" name="id" value="{{!empty($staff->id) ? $staff->id : ''}}">
      <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
      <div class="box-body">
        <div class="form-group">
          <label for="userLabel">用户</label>
          <input type="text" class="form-control" id="name" placeholder="用户" name="name" value="{{!empty($staff->name)? $staff->name : ''}}">
        </div>
        @if(empty($staff->id))
        <div class="form-group">
          <label for="userLabel">密码</label>
          <input type="password" class="form-control" id="password" placeholder="密码" name="password">
        </div>
        <div class="form-group">
          <label for="userLabel">确认密码</label>
          <input type="password" class="form-control" id="confirm" placeholder="确认密码" name="confirm">
        </div>
        @endif
        <div class="form-group">
          <label for="perLabel">角色</label>
          <select class="form-control select2" multiple="multiple" data-placeholder="角色" style="width: 100%;" name="role[]">
            @foreach($roles as $key=>$value)
              @if(empty($role))
                <option value="{{$key}}">{{$value}}</option>
              @else
                <option value="{{$key}}" {{(in_array($key, array_keys($role))) ? 'selected' : ''}}>{{$value}}</option>
              @endif
            @endforeach
          </select>
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <button type="submit" class="btn btn-primary">提交</button>
      </div>
    </form>
  </div>
@endsection
