@extends('layouts.app')

@section('js')
  <script src="/public/default/js/vue.js"></script>
  <script src="/public/default/js/common/selectPlaceholder.js"></script>
  <script>
    $(function(){
      var grade = new Vue({
        el: '#grades',
        delimiters: ['<%','%>'],
        data: {
          selected: "{{!empty($vueOptions['grade']['selected'])?$vueOptions['grade']['selected']:''}}",
          options:{!! json_encode($vueOptions['grade']['options']) !!}
        }
      });
      grade.options.unshift({'value':'', 'text':'全部年级'});
      getPlaceholder("{{empty($vueOptions['grade']['selected'])}}", '#grade', "--请选择年级--");

      // setup datatables
      var table = $('#example').DataTable( {
        dom: "<'row'<'col-sm-6'c><'col-sm-2'l><'col-sm-4'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        bPaginate: true,
        order: [[ 0, "desc" ]],
        buttons: [ 'copy', {
          extend: 'excel',
          filename: '学生列表'
        }, 'pdf', 'colvis' ]
      } );
      table.buttons().container().appendTo( '#example_wrapper .col-sm-6:eq(0)' );
    });
  </script>
@endsection

@section('content')
  <div class="box">
    <div class="box-body">
      <div class="row">
        <div class="input-group" style="width:100%;">
          <form method="post" action="/student/index" id="myForm">
            {{ csrf_field() }}
            <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;">
              <input type="text" class="form-control" id="name"  name="name" placeholder="姓名" value="{{!empty($name) ? $name : ''}}">
            </div>
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;" id="grades">
              <select class="form-control" id="grade" name="grade" v-model="selected">
                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
              </select>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;">
              <input type="text" class="form-control" id="phoneNum"  name="phoneNum" placeholder="联系电话" value="{{!empty($phoneNum) ? $phoneNum : ''}}">
            </div>
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;">
              <button type="button" class="btn btn-success" onClick="location.href='/student/index/edit'">新建</button>
              <button type="submit" class="btn btn-info">查找</button>
            </div>
          </form>
        </div>
      </div>
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
            <th>年级</th>
            <th>联系电话</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($students))
            @foreach ($students as $student)
              <tr>
                <td><a href="/student/index/edit?id={{$student['id']}}&preview=1">{{$student['id']}}</a></td>
                <td>{{$student['name']}}</td>
                <td>{{$student['grade']}}</td>
                <td>{{$student['phoneNum']}}</td>
                <td>
                  <a class="btn btn-info" href="/student/index/edit?id={{$student['id']}}&preview=1">查看</a>
                  <a class="btn btn-info" href="/student/index/edit?id={{$student['id']}}">修改</a>
                  <a class="btn btn-warning" href="/student/index/delete?id={{$student['id']}}" onclick="return confirm('确认删除！')">删除</a>
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
