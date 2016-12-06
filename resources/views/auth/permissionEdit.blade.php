@extends('layouts.app')

@section('content')
  <div class="box box-primary">
    <!-- form start -->
    <form role="form" method="post" action="{{$controlUrl}}/update">
      {{ csrf_field() }}
      <input type="hidden" name="id" value="{{!empty($pms->id) ? $pms->id : ''}}">
      <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
      <div class="box-body">
        <div class="form-group">
          <label for="display_name">名称</label>
          <input type="text" class="form-control" id="display_name" placeholder="名称" name="display_name" value="{{!empty($pms->display_name)? $pms->display_name : ''}}">
        </div>
        <div class="form-group">
          <label for="nameLabel">代码编号</label>
          <input type="text" class="form-control" id="nameLabel" placeholder="代码编号" name="name" value="{{!empty($pms->name)? $pms->name : ''}}">
        </div>
        <div class="form-group">
          <label for="description">描述(可选)</label>
          <textarea class="form-control" rows="10" class="form-control" id="description" placeholder="Enter ..." name="description">{{!empty($pms->description)? $pms->description : ''}}</textarea>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" class="btn btn-primary">提交</button>
      </div>
    </form>
  </div>
@endsection
