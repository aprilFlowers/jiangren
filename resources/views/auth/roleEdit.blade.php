@extends('layouts.app')

@section('js')
  <script src="/public/default/js/ckeditor/ckeditor.js"></script>
  <script>
    $(function(){
      $(".select2").select2();
    });
  </script>
@endsection

@section('content')
  <div class="box box-primary">
    <!-- form start -->
    <form role="form" method="post" action="{{$controlUrl}}/update">
      {{ csrf_field() }}
      <input type="hidden" name="id" value="{{!empty($role->id) ? $role->id : ''}}">
      <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
      <div class="box-body">
        <div class="form-group">
          <label for="display_name">名称</label>
          <input type="text" class="form-control" id="display_name" placeholder="名称" name="display_name" value="{{!empty($role->display_name)? $role->display_name : ''}}">
        </div>
        <div class="form-group">
          <label for="nameLabel">代码编号</label>
          <input type="text" class="form-control" id="nameLabel" placeholder="代码编号" name="name" value="{{!empty($role->name)? $role->name : ''}}">
        </div>
        <div class="form-group">
          <label for="perLabel">权限</label>
          <select class="form-control select2" multiple="multiple" data-placeholder="权限" style="width: 100%;" name="permission_id[]">
            @foreach($permission as $key=>$value)
              @if(empty($per))
                <option value="{{$key}}">{{$value}}</option>
              @else
                <option value="{{$key}}" {{(in_array($key, array_keys($per))) ? 'selected' : ''}}>{{$value}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="description">描述(可选)</label>
          <textarea class="form-control" rows="10" class="form-control" id="description" placeholder="Enter ..." name="description">{{!empty($role->description)? $role->description : ''}}</textarea>
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <button type="submit" class="btn btn-primary">提交</button>
      </div>
    </form>
  </div>
@endsection
