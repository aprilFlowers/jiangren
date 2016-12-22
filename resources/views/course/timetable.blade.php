@extends('layouts.app')

@section('js')
    <!-- jQuery UI 1.11.4 -->
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
            var week = new Vue({
                el  : '#weeks',
                data: {
                    week: "{{!empty($week['selected']) ? $week['selected'] : ''}}"
                }
            });
            getPlaceholder("{{empty($teacher['selected'])}}", '#teacher', "--请选择老师--");
            week.week = "{{$week['selected']}}";

            $('.item').draggable({
                revert:true,
                proxy:'clone',
                disabled : {{$admin == 'admin' ? 'false' : 'true'}}
            });
            $('.item').droppable({
                accept:'.assigned',
                onDragEnter:function(e,source){
                    $(source).addClass('trash');
                },
                onDragLeave:function(e,source){
                    $(source).removeClass('trash');
                },
                onDrop:function(e,source){
                    if(confirm('确认删除！')) {
                        $.ajax({
                            url : '/course/markTimetable/deleteData',
                            dataType : 'json',
                            data :{
                                id : $(source).attr('id')
                            },
                            success : function(data) {
                              $(source).remove();
                                alert(data.errorMsg);
                                //window.location = '/course/timetable';
                            }
                        });
                    }
                }
            });
            $('.right td.drop').droppable({
                onDragEnter:function(){
                    //enter table
                    $(this).addClass('over');
                },
                onDragLeave:function(){
                    //leave table
                    $(this).removeClass('over');
                },
                onDrop:function(e,source){
                    $(this).removeClass('over');
                    if ($(source).hasClass('assigned')){
                        //update
                        $.ajax({
                            url : '/course/markTimetable/saveData',
                            dataType : 'json',
                            data :{
                                id : $(source).attr('id'),
                                index : $(this).attr('id'),
                                content : $(source).html()
                            },
                            success : function(data) {
                              $(this).append(source);
                                alert(data.errorMsg);
                                //window.location = '/course/timetable';
                            }
                        });
                    } else {
                        //add
                        var c = $(source).clone().addClass('assigned');
                        c.draggable({
                            revert:true
                        });
                        // save event data
                        $.ajax({
                            url : '/course/markTimetable/saveData',
                            dataType : 'json',
                            data :{
                                index : $(this).attr('id'),
                                content : c.html()
                            },
                            success : function(data) {
                              $(this).append(c);
                              alert(data.errorMsg);
                              if (data.id > 0) {
                              c.attr('id', data.id);
                              }
                                //window.location = '/course/timetable';
                            }
                        });
                    }
                }
            });
        })
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
                        <div class="col-xs-12 col-md-6 col-lg-2" style="margin: 5px 0 5px 0;" id="weeks">
                            <input type="text" class="Wdate form-control" style="height:34px;border-color: #d2d6de;" id="week" name="week" placeholder="请选择周数" v-model="week" onClick="WdatePicker({skin:'whyGreen',isShowWeek:true})"/>
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
                <!-- course list -->
                <div class="col-md-3">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                          <h4 class="box-title">课程列表</h4>
                        </div>
                        <div class="box-body">
                            <!-- the events -->
                            <div id="external-events" style="max-height:500px; overflow:auto;">
                                @if(!empty($courseDragable))
                                    @foreach($courseDragable as $courseInfo)
                                        <div class="item" id="{{$courseInfo['id']}}" style="background: #{{$courseInfo['color']}}">{{$courseInfo['courseName']}} | {{$courseInfo['teacher']}} | {{$courseInfo['student']}}</div>
                                    @endforeach
                                @endif

                            </div>
                            <div class="demo-info" style="margin-bottom:10px">
                              <div class="demo-tip icon-tip">&nbsp;</div>
                              <div>添加课程：请将课程拖拽至课程表中</div>
                              <div>删除课程：请将课程拖回至本列表中</div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <!-- end course list -->
                <!-- right timetable -->
                <div class="col-md-9">
                    <div class="right box box-solid">
                    <div class="box-body no-padding">
                            <h2 style="text-align: center;">{{$time}}</h2>
                            <table class="timetable" cellspacing="0" width="100%" style="text-align:center; word-break:break-all; word-wrap:break-word;">
                                <thead>
                                <tr>
                                    <td class="blank" width="12.5%">节次\星期</td>
                                    <td class="title" width="12.5%">星期一</td>
                                    <td class="title" width="12.5%">星期二</td>
                                    <td class="title" width="12.5%">星期三</td>
                                    <td class="title" width="12.5%">星期四</td>
                                    <td class="title" width="12.5%">星期五</td>
                                    <td class="title" width="12.5%">星期六</td>
                                    <td class="title" width="12.5%">星期日</td>
                                </tr>
                                </thead>
                                @foreach ($sectionList as $sec)
                                    <tr>
                                        <td class="time">{{$sec['name']}}</td>
                                        @foreach($sec['id'] as $id)
                                        <td class="drop" id="{{$id}}">
                                            @if (!empty($table[$id]))
                                                @foreach ($table[$id] as $t)
                                                    <div class="item assigned {{$t['status'] ? 'clicked' : ''}}" id="{{$t['id']}}" style="background: #{{$t['color']}}">{{$t['content']}}</div>
                                                @endforeach
                                            @endif
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
    </div>
@endsection
