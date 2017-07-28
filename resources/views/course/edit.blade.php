@extends('layouts.app')

@section('js')
    <script src="/public/default/js/vue.js"></script>
    <script src="/public/default/js/My97DatePicker/WdatePicker.js"></script>
    <script src="/public/default/js/common/selectPlaceholder.js"></script>
    <script>
        $(function(){
            new Vue({
                el: '#teachers',
                delimiters: ['<%','%>'],
                data: {
                    selected: "{{!empty($teacher['selected']) ? $teacher['selected'] : '' }}",
                    options:{!! json_encode(array_replace($vueOptions['teacher']['options'])) !!}
                }
            });
            new Vue({
                el: '#stuGroups',
                delimiters: ['<%','%>'],
                data: {
                    selected: "{{!empty($stuGroup['selected']) ? $stuGroup['selected']: '' }}",
                    options:{!! json_encode(array_replace($vueOptions['stuGroup']['options'])) !!}
                }
            });
            $('#teacher').click(function(){
                $(this).change(function(){
                    var cId = $(this).val();
                    $.ajax({
                        type      : "get",
                        url       : "/course/getStuGroup",
                        data      : {
                            cId  : cId,
                        },
                        beforeSend: function(){
                            $('#loading').attr('hidden', false);
                        },
                        dataType  : 'json',
                        success   : function(data){
                            $('#loading').attr('hidden', true);
                            $('#stuGroup').empty();
                            $('#stuGroup').append('<option selected="true" disabled>请选择组合</option>');
                            var a = '';
                            for(var stuGroup in data) {
                                    a += '<option value="' + data[stuGroup].value + '">' + data[stuGroup].text + '</option>';
                            }
                            $('#stuGroup').append(a);
                        }
                    });
                });
            });
            getPlaceholder("{{empty($teacher['selected'])}}", '#teacher', '请选择老师');
            getPlaceholder("{{empty($stuGroup['selected'])}}", '#stuGroup', '请选择组合');
        });
    </script>
@endsection

@section('content')
    <div class="box">
        <form method="post" action="{{$controlUrl}}/update">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{!empty($course['id']) ? $course['id'] : ''}}">
            <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
            <div class="box-body">
                <!-- body -->
                <div class="row">
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4" id="teachers">
                            <label for="teacher">老师</label>
                            <select class="form-control" id="teacher" name="teacher" v-model="selected">
                                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4" id="stuGroups">
                            <label for="stuGroup">学生组合</label>
                            <select class="form-control" id="stuGroup" name="stuGroup" v-model="selected">
                                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="openTime">开始时间</label>
                            <input type="text" class="Wdate form-control" style="height:34px;border-color: #d2d6de;" id="openTime" name="openTime" placeholder="开始时间" onClick="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm',
    minDate:'2008-03-08 00:00:00'})" value="{{!empty($openTime) ? $openTime : ''}}">
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="endTime">结束时间</label>
                            <input type="text" class="Wdate form-control" style="height:34px;border-color: #d2d6de;" id="endTime" name="endTime" placeholder="结束时间" onClick="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm',
    minDate:'2008-03-08 00:00:00'})" value="{{!empty($endTime) ? $endTime : ''}}">
                        </div>
                    </div>
                </div>
                <div class="input-group" style="width:100%; margin-bottom:20px;">
                    <button type="submit" class="btn btn-primary" style="margin-right:20px;">提交</button>
                </div>
            </div>
        </form>
        <!-- loading -->
        <div class="overlay" id="loading" hidden>
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
    <!-- /.box -->
@endsection
