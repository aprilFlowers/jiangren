@extends('layouts.app')

@section('css')
    <style type="text/css">
        .showBox {
            width: 100%;
            height: 100%;
            background-color: #777;
            opacity: 0.7;
            filter: alpha(opacity=70);
            position: absolute;
            top: 0;
            left: 0;
            z-index: 10;
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="box">
        <form method="post" action="/course/timetable" id="myForm">
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
                        <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;" id="openTimes">
                            <input type="text" class="Wdate form-control" style="height:34px;border-color: #d2d6de;" id="openTime" name="openTime" placeholder="请选择周数" v-model="openTime" onClick="WdatePicker({skin:'whyGreen',isShowWeek:true})"/>
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
        <div class="row">
            <!-- right timetable -->
            <div class="col-md-9">
                <div class="right box box-solid">
                <div class="box-body no-padding" id="sortable">
                        <h2 style="text-align: center;">{{$time}}</h2>
                        <table class="timetable" cellspacing="0" width="100%" style="text-align:center; word-break:break-all; word-wrap:break-word;">
                            <thead>
                            <tr>
                                <td class="blank" width="12.5%">时间\星期</td>
                                <td class="title" width="12.5%">星期日</td>
                                <td class="title" width="12.5%">星期一</td>
                                <td class="title" width="12.5%">星期二</td>
                                <td class="title" width="12.5%">星期三</td>
                                <td class="title" width="12.5%">星期四</td>
                                <td class="title" width="12.5%">星期五</td>
                                <td class="title" width="12.5%">星期六</td>
                            </tr>
                            </thead>
                            @foreach ($lessons as $lesson)
                                <tr>
                                    <td class="time">{{$lesson['name']}}</td>
                                    @foreach($lesson['id'] as $id)
                                    <td class="drop" data-index="{{$id}}" height="50px">
                                      @foreach ($table as $k => $t)
                                          @if ($id == $k)
                                              <div>共有{{$t['total']}}节课</div>
                                              <div><a onclick="showCourseInfos('{{$t['info']}}', '{{$t['date']}}')" href="#">查看详情</a></div>
                                          @endif
                                      @endforeach
                                    </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- end timetable -->
        </div>
      </div>
        <!-- /.box-body -->
      <div class="showBox"></div>
        <!-- /.showbox -->
    </div>
@endsection

@section('js')
    <!-- jQuery EasyUI 1.5 -->
    <script src="/public/default/js/jquery.easyui.min.js"></script>
    <script src="/public/default/js/vue.js"></script>
    <script src="/public/default/js/common/selectPlaceholder.js"></script>
    <script src="/public/default/js/My97DatePicker/WdatePicker.js"></script>
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
                        el  : '#openTimes',
                        data: {
                            openTime: "{{!empty($vueOptions['openTime']['selected']) ? $vueOptions['openTime']['selected'] : ''}}"
                        }
                    });
        });

        function showCourseInfos (courseInfos, date) {
            $('.showBox').show();
            $('.showBox').append("<div id='courseInfo' style='color:white;text-align: center'> <h2>"+date+" </h2> </div>");
            console.log(courseInfos);
            $.each($.parseJSON(courseInfos), function(index, content) {
                $('#courseInfo').append("<div style='color:white;align-content: center'>" + content.course + "</div>");
            });
        }
        $('.showBox').on('click', function () {
            $(this).hide();
            $('#courseInfo').remove();
        });
    </script>

@endsection