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
                    <th>性别</th>
                    <th>联系电话</th>
                    <th>操作</th>
                </tr>
                @if(!empty($teachers))
                    @foreach ($teachers as $teacher)
                        <tr>
                            <td><a href="{{$controlUrl}}/edit?id={{$teacher['id']}}">{{$teacher['id']}}</a></td>
                            <td>{{$teacher['name']}}</td>
                            <td>
                                @if($teacher['sex'] == 1) 男
                                @elseif($teacher['sex'] == 2) 女
                                @endif
                            </td>
                            <td>{{$teacher['phoneNum']}}</td>
                            <td>
                                <form method="post" action="{{$controlUrl}}/delete?id={{$teacher['id']}}" onsubmit="return confirm('确定删除！')">
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
