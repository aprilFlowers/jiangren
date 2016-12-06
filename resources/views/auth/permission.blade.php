@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-body">
            <button type="submit" class="btn btn-info pull-right" onClick="location.href='{{$controlUrl}}/edit'">新建</button>
        </div>
    </div>
    <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <th>权限</th>
                    <th>操作</th>
                </tr>
                @if(!empty($pms))
                    @foreach ($pms as $p)
                        <tr>
                            <td><a href="{{$controlUrl}}/edit?id={{$p->id}}">{{$p->id}}</a></td>
                            <td>{{$p->display_name}}</td>
                            <td>
                                <a class="btn btn-info" href="{{$controlUrl}}/edit?id={{$p->id}}">修改</a>
                                <a class="btn btn-warning" href="{{$controlUrl}}/delete?id={{$p->id}}" onclick="return confirm('确认删除！')">删除</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection
