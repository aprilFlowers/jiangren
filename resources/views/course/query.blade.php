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
                    selected: "{{!empty($teacher['selected'])?$teacher['selected']:null}}",
                    options:{!! json_encode($teacher['options']) !!}
                }
            });
            var student = new Vue({
                el: '#students',
                delimiters: ['<%','%>'],
                data: {
                    selected: "{{!empty($student['selected'])?$student['selected']:null}}",
                    options:{!! json_encode($student['options']) !!}
                }
            });
            var openTime = new Vue({
                el  : '#openTime',
                data: {
                    openTime: "{{!empty($openTime['selected']) ? $openTime['selected'] : ''}}"
                }
            });
            var endTime = new Vue({
                el  : '#endTime',
                data: {
                    endTime: "{{!empty($endTime['selected']) ? $endTime['selected'] : ''}}"
                }
            });

            $('#openTime').datepicker({});
            $('#endTime').datepicker({});

            getPlaceholder("{{empty($teacher['selected'])}}", '#teacher', "--请选择老师--");
            getPlaceholder("{{empty($student['selected'])}}", '#student', "--请选择学生--");
        });

        function clickCourse(cid, sid){
            if(confirm('确认课程!')){
                $.ajax({
                    type:'get',
                    url:"/course/query/clickCourse",
                    data:{
                        cid : cid,
                        sid : sid,
                    },
                    dataType  : 'json',
                    success   : function(data){
                        alert(data.errorMsg);
                    }
                });
            }
        }
    </script>
@endsection

@section('content')
    <div class="box">
        <form method="post" action="{{$controlUrl}}" id="myForm">
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
                            <input type="text" class="form-control pull-right" id="openTime" name="openTime"
                                   placeholder="开始时间" v-model="openTime" onClick="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss',
minDate:'2008-03-08 11:30:00'})">
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;">
                            <input type="text" class="form-control pull-right" id="endTime" name="endTime"
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
            @if (!empty($_token))
            <div class="col-md-12">
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>名称</th>
                            <th>学生</th>
                            <th>老师</th>
                            <th>课时</th>
                            <th>开始时间</th>
                            <th>结束时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        @if(!empty($course))
                            @foreach ($course as $c)
                                <tr>
                                    <td>{{$c['name']}}</td>
                                    <td>{{$c['studentName']}}</td>
                                    <td>{{$c['teacherName']}}</td>
                                    <td>{{$c['period']}}</td>
                                    <td>{{$c['start']}}</td>
                                    <td>{{$c['end']}}</td>
                                    <td>{{$c['status']}}</td>
                                    <td>
                                        @if($admin == 'admin')
                                        <button type="button" class="btn btn-warning" onclick="clickCourse({{$c['id']}}, {{$c['student']}})">确认课程</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
            @endif
        </div>
        <!-- /.box-body -->
    </div>
@endsection
