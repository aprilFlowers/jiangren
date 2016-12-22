@extends('layouts.app')

@section('js')
  <script src="/public/default/js/jscolor.min.js"></script>
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{$controlUrl}}/update">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{!empty($subject['id']) ? $subject['id'] : ''}}">
            <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
            <div class="box-body">
                <!-- body -->
                <div class="row">
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="name">名称</label>
                            <input id="name" name="name" type="text" class="form-control" placeholder="名称" value="{{!empty($subject['name']) ? $subject['name'] : ''}}">
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="color">课程表标签颜色</label>
                            <input id="color" name="color" class="jscolor form-control" value="{{!empty($subject['color']) ? $subject['color'] : '00c0ef'}}">
                        </div>
                    </div>
                </div>
                <div class="input-group" style="width:100%; margin-bottom:20px;">
                    <button type="submit" class="btn btn-primary" style="margin-right:20px;">提交</button>
                </div>
            </div>
        </form>
        <!-- /.search-box -->
    </div>
    <!-- /.box -->
@endsection
