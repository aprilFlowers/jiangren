@extends('layouts.app')

@section('js')
  <script>
    $(function(){
      // setup datatables
      var table = $('#example').DataTable( {
        dom: "<'row'<'col-sm-6'c><'col-sm-2'l><'col-sm-4'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        bPaginate: true,
        order: [[ 0, "desc" ]],
        buttons: [ 'copy', {
          extend: 'excel',
          filename: '教师列表'
        }, 'pdf', 'colvis' ]
      } );
      table.buttons().container().appendTo( '#example_wrapper .col-sm-6:eq(0)' );

    });
  </script>
@endsection


@section('content')
  <div class="box">
    <div class="box-body">
      <button type="submit" class="btn btn-info pull-right" onClick="location.href='/teacher/index/edit'">新建</button>
    </div>
  </div>
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>性别</th>
            <th>联系电话</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($teachers))
            @foreach ($teachers as $teacher)
              <tr>
                <td><a href="/teacher/index/edit?id={{$teacher['id']}}">{{$teacher['id']}}</a></td>
                <td>{{$teacher['name']}}</td>
                <td>
                  @if($teacher['sex'] == 1) 男
                  @elseif($teacher['sex'] == 2) 女
                  @endif
                </td>
                <td>{{$teacher['phoneNum']}}</td>
                <td>
                  <a class="btn btn-info" href="/teacher/index/edit?id={{$teacher['id']}}">修改</a>
                  <a class="btn btn-warning" href="/teacher/index/delete?id={{$teacher['id']}}" onclick="return confirm('确认删除！')">删除</a>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- /.box -->
@endsection
