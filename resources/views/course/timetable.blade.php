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
                        $(source).remove();
                        $.ajax({
                            url : '/course/markTimetable/deleteData',
                            dataType : 'json',
                            data :{
                                id : $(source).attr('id')
                            },
                            success : function(data) {
                                alert(data.errorMsg);
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
                        $(this).append(source);
                    } else {
                        //add
                        var c = $(source).clone().addClass('assigned');
                        $(this).append(c);
                        c.draggable({
                            revert:true
                        });
                        // save event data
                        $.ajax({
                            url : '/course/markTimetable/saveData',
                            dataType : 'json',
                            data :{
                                id : $(this).attr('id'),
                                content : c.html()
                            },
                            success : function(data) {
                                alert(data.errorMsg);
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
        <div class="row">
            <div class="box-body" style="min-height: 700px">
                <!-- course list -->
                <div class="col-md-3">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h4 class="box-title">基本课程</h4>
                        </div>
                        <div class="left box-body">
                            <!-- the events -->
                            <div id="external-events">
                                @if(!empty($courseNames))
                                    @foreach($courseNames as $name => $courseInfo)
                                        <div class="item" id="{{$courseInfo['id']}}">{{$name}} | {{$courseInfo['teacher']}} | {{$courseInfo['student']}}</div>
                                    @endforeach
                                @endif

                                <div class="demo-info" style="margin-bottom:10px">
                                    <div class="demo-tip icon-tip">&nbsp;</div>
                                    <div>请将课程拖拽至课程表中</div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <!-- end course list -->
                <!-- right timetable -->
                <div class="col-md-9">
                    <div class="box-body no-padding">
                        <div class="right">
                            <h2 style="text-align: center;">{{$time}}</h2>
                            <table class="table table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <td class="blank">节次\星期</td>
                                    <td class="title">星期一</td>
                                    <td class="title">星期二</td>
                                    <td class="title">星期三</td>
                                    <td class="title">星期四</td>
                                    <td class="title">星期五</td>
                                    <td class="title">星期六</td>
                                    <td class="title">星期日</td>
                                </tr>
                                </thead>
                                @foreach ($sectionList as $sec)
                                    <tr>
                                        <td class="time">{{$sec['name']}}</td>
                                        @foreach($sec['id'] as $id)
                                        <td class="drop" id="{{$id}}">
                                            @if (!empty($table[$id]))
                                                @foreach ($table[$id] as $t)
                                                    <div class="item assigned {{$t['status'] ? 'clicked' : ''}}" id="{{$t['id']}}">{{$t['content']}}</div>
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
