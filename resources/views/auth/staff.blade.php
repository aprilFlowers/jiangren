@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-body">
            <button type="submit" class="btn btn-info pull-right" onClick="location.href='{{$controlUrl}}/edit'">新建</button>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <th>用户</th>
                    <th class="hidden-xs">角色</th>
                    <th>操作</th>
                </tr>
                @if(!empty($staff))
                    @foreach ($staff as $s)
                        <tr>
                            <td><a href="{{$controlUrl}}/edit?id={{$s->id}}">{{$s->id}}</a></td>
                            <td>{{$s->name}}</td>
                            <td class="hidden-xs">
                                @if(!empty($s->roles))
                                  @foreach ($s->roles as $role)
                                    {{$role->display_name}},
                                  @endforeach
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-info" href="{{$controlUrl}}/edit?id={{$s->id}}">修改</a>
                                <a class="btn btn-warning" href="{{$controlUrl}}/delete?id={{$s->id}}" onclick="return confirm('确认删除！')">删除</a>
                                <a class="btn btn-primary" href="{{$controlUrl}}/changePWD?id={{$s->id}}">修改密码</a>
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
