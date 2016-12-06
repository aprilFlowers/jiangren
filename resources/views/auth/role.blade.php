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
                    <th>角色</th>
                    <th class="hidden-xs">权限</th>
                    <th>操作</th>
                </tr>
                @if(!empty($role))
                    @foreach ($role as $r)
                        <tr>
                            <td><a href="{{$controlUrl}}/edit?id={{$r->id}}">{{$r->id}}</a></td>
                            <td>{{$r->role}}</td>
                            <td class="hidden-xs">{{$r->permission}}</td>
                            <td>
                                <a class="btn btn-info" href="{{$controlUrl}}/edit?id={{$r->id}}">修改</a>
                                <a class="btn btn-warning" href="{{$controlUrl}}/delete?id={{$r->id}}" onclick="return confirm('确认删除！')">删除</a>
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
