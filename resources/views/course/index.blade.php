@extends('layouts.app')

@section('js')
  <script src="/public/default/js/My97DatePicker/WdatePicker.js"></script>
  <script src="/public/default/js/vue.js"></script>
  <script src="/public/default/js/common/selectPlaceholder.js"></script>
  <script>
    $(function(){
      var teacher = new Vue({
        el: '#teachers',
        delimiters: ['<%','%>'],
        data: {
          selected: "{{!empty($vueOptions['teacher']['selected'])?$vueOptions['teacher']['selected']:''}}",
          options:{!! json_encode($vueOptions['teacher']['options']) !!}
        }
      });
      @if(\Entrust::hasRole('admin') || \Entrust::hasRole('student'))
        teacher.options.unshift({'value':'', 'text':'全部教师'});
      @endif
      var student = new Vue({
        el: '#students',
        delimiters: ['<%','%>'],
        data: {
          selected: "{{!empty($vueOptions['student']['selected'])?$vueOptions['student']['selected']:''}}",
          options:{!! json_encode($vueOptions['student']['options']) !!}
        }
      });
      @if(\Entrust::hasRole('admin') || \Entrust::hasRole('teacher'))
        student.options.unshift({'value':'', 'text':'全部学生'});
      @endif
      var openTime = new Vue({
        el  : '#openTime',
        data: {
          openTime: "{{!empty($vueOptions['openTime']['selected']) ? $vueOptions['openTime']['selected'] : $openTime}}"
        }
      });
      var endTime = new Vue({
        el  : '#endTime',
        data: {
          endTime: "{{!empty($vueOptions['endTime']['selected']) ? $vueOptions['endTime']['selected'] : $endTime}}"
        }
      });

      // setup datatables
      var table = $('#example').DataTable( {
        dom: "<'row'<'col-sm-6'c><'col-sm-2'l><'col-sm-4'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        bPaginate: true,
        order: [[ 6, "desc" ], [ 4, "asc" ]],
        buttons: [ 'copy', {
          extend: 'excel',
          filename: '课程列表'
        }, 'pdf', 'colvis' ]
      } );
      table.buttons().container().appendTo( '#example_wrapper .col-sm-6:eq(0)' );
    });
  </script>
  <script>
    function clickCourse(cid, sid){
      if(confirm('确认课程!')){
        $.ajax({
          type:'get',
          url:"/course/index/clickCourse",
          data:{
            cid : cid,
            sid : sid,
          },
          dataType  : 'json',
          success   : function(data){
            alert(data.errorMsg);
            $('#confirmBtn_'+cid).removeClass('btn-warning').addClass('btn-default');
            $('#confirmBtn_'+cid).attr('disabled', true).html('已确认课程');
          }
        });
      }
    }
  </script>
@endsection

@section('content')
  <div class="box">
    <form method="post" action="/course/index" id="myForm">
      {{ csrf_field() }}
      <input type="hidden" name="type_name"
                           value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">

      <div class="box-body">
        <div class="row">
          <div class="input-group" style="width:100%;">
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;" id="teachers">
              <select class="form-control" id="teacher" name="teacher" v-model="selected">
                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
              </select>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;" id="students">
              <select class="form-control" id="student" name="student" v-model="selected">
                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
              </select>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;">
              <input type="text" class="Wdate form-control" style="height:34px;border-color: #d2d6de;" id="openTime" name="openTime"
                                                                                                                     placeholder="开始时间" v-model="openTime" onClick="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss',
                                                                                                                                                                   minDate:'2008-03-08 11:30:00'})">
            </div>
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;">
              <input type="text" class="Wdate form-control" style="height:34px;border-color: #d2d6de;" id="endTime" name="endTime"
                                                                                                                    placeholder="结束时间" v-model="endTime" onClick="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss',
                                                                                                                                                                 minDate:'2008-03-08 11:30:00'})">
            </div>
            <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;">
              <button type="submit" class="btn btn-info">查找</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <!-- /.search-box -->
  <div class="box">
    <div class="box-body" style="min-height: 700px">
      <div class="col-md-12">
        <div class="box-body">
          <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>科目</th>
                <th>老师</th>
                <th>学生</th>
                <th>开始时间</th>
                <th>结束时间</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($course))
                @foreach ($course as $c)
                  @if(!empty($c['subject']))
                  <tr>
                    <td>{{$c['id']}}</td>
                    <td>{{$subjects[$c['subject']]['name']}}</td>
                    <td>{{$teachers[$c['teacher']]['name']}}</td>
                    <td>{{$students[$c['student']]['name']}}</td>
                    <td>{{$c['start']}}</td>
                    <td>{{$c['end']}}</td>
                    <td>
                      @if($c['status'] == 2)
                        <button type="button" class="btn btn-default" disabled>已确认课程</button>
                      @elseif($admin == 'admin' && $c['status'] == 1)
                        <button id="confirmBtn_{{$c['id']}}" type="button" class="btn btn-warning" onclick="clickCourse({{$c['id']}}, {{$c['student']}})">确认课程</button>
                      @endif
                    </td>
                  </tr>
                  @endif
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
@endsection
