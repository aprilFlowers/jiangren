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
                    <th>姓名</th>
                    <th>年级</th>
                    <th>联系电话</th>
                    <th>操作</th>
                </tr>
                @if(!empty($students))
                    @foreach ($students as $student)
                        <tr>
                            <td><a href="{{$controlUrl}}/edit?id={{$student['id']}}">{{$student['id']}}</a></td>
                            <td>{{$student['name']}}</td>
                            <td>{{$student['grade']}}</td>
                            <td>{{$student['phoneNum']}}</td>
                            <td>
                                <form method="post" action="{{$controlUrl}}/delete?id={{$student['id']}}" onsubmit="return confirm('确定删除！')">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-warning">删除</button>
                                </form>
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
