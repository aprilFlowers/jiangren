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
                    <th>操作</th>
                </tr>
                @if(!empty($allSubject))
                    @foreach ($allSubject as $subject)
                        <tr>
                            <td><a href="{{$controlUrl}}/edit?id={{$subject['id']}}">{{$subject['id']}}</a></td>
                            <td>{{$subject['name']}}</td>
                            <td>
                                <a class="btn btn-info" href="{{$controlUrl}}/edit?id={{$subject['id']}}">修改</a>
                                <a class="btn btn-warning" href="{{$controlUrl}}/delete?id={{$subject['id']}}" onclick="return confirm('确认删除！')">删除</a>
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
