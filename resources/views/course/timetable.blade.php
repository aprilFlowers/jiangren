@extends('layouts.app')

@section('js')
    <script src="/public/default/js/vue.js"></script>
    <script src="/public/default/js/common/selectPlaceholder.js"></script>
    <script src="/public/default/js/My97DatePicker/WdatePicker.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="/public/default/js/jquery-ui.min.js"></script>
    <!-- fullCalendar 2.2.5 -->
    <script src="/public/default/js/moment.min.js"></script>
    <script src="/public/default/js/fullcalendar.min.js"></script>
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

            week.week = "{{$week['selected']}}";
            getPlaceholder("{{empty($teacher['selected'])}}", '#teacher', "--请选择老师--");

            $('#calendar').fullCalendar({
                defaultView : 'agendaWeek',
                defaultDate : "{{$time}}",
                axisFormat:'h(:mm)t',
                dayNamesShort:['星期日', '星期一', '星期二', '星期三',
                    '星期四', '星期五', '星期六'],
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek'
                },
                buttonText: {
                    today: '今天',
                    week: 'week',
                },
                //Random default events
                events : {!! $datas !!},
                editable : false,
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
        <div class="box-body" style="min-height: 700px">
            @if (!empty($_token))
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body no-padding">
                        <!-- THE CALENDAR -->
                        <div id="calendar"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
            </div>
            @endif
        </div>
        <!-- /.box-body -->
    </div>
@endsection
