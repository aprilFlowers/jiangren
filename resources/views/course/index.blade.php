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
                    <th>名称</th>
                    <th>学生</th>
                    <th>老师</th>
                    <th>课时</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                @if(!empty($courses))
                    @foreach ($courses as $course)
                        <tr>
                            <td><a href="{{$controlUrl}}/edit?id={{$course['id']}}">{{$course['id']}}</a></td>
                            <td>{{$course['name']}}</td>
                            <td>{{$course['studentName']}}</td>
                            <td>{{$course['teacherName']}}</td>
                            <td>{{$course['period']}}</td>
                            <td>{{$course['status']}}</td>
                            <td>
                                <a class="btn btn-info" href="{{$controlUrl}}/edit?id={{$course['id']}}">修改</a>
                                <a class="btn btn-warning" href="{{$controlUrl}}/delete?id={{$course['id']}}" onclick="return confirm('确认删除！')">删除</a>
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
