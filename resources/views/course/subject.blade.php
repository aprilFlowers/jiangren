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
          filename: '学科列表'
        }, 'pdf', 'colvis' ]
      } );
      table.buttons().container().appendTo( '#example_wrapper .col-sm-6:eq(0)' );
    });
  </script>
@endsection

@section('content')
  <div class="box">
    <div class="box-body">
      <button type="submit" class="btn btn-info pull-right" onClick="location.href='/course/subject/edit'">新建</button>
    </div>
  </div>
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>名称</th>
            <th>标签颜色</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($subjects))
            @foreach ($subjects as $subject)
              <tr>
                <td><a href="/course/subject/edit?id={{$subject['id']}}">{{$subject['id']}}</a></td>
                <td>{{$subject['name']}}</td>
                <td><input disabled style="background: #{{$subject['color']}}"></input></td>
                <td>
                  @if ($subject['status'] == 1)
                  <a class="btn btn-info" href="/course/subject/edit?id={{$subject['id']}}">修改</a>
                  <a class="btn btn-warning" href="/course/subject/delete?id={{$subject['id']}}" onclick="return confirm('确认删除！')">删除</a>
                  @elseif ($subject['status'] == 0)
                  <a class="btn btn-default" href="#" disabled>已删除</a>
                  @endif
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
