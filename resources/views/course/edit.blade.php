@extends('layouts.app')

@section('js')
    <script src="/public/default/js/vue.js"></script>
    <script src="/public/default/js/common/selectPlaceholder.js"></script>
    <script>
        $(function () {
            var subject = new Vue({
                el: '#subjects',
                delimiters: ['<%', '%>'],
                data: {
                    selected: "{{!empty($subject['selected']) ? $subject['selected'] : null }}",
                    options:{!! json_encode($subject['options']) !!}
                }
            });
            var student = new Vue({
                el: '#students',
                delimiters: ['<%', '%>'],
                data: {
                    selected: "{{!empty($student['selected']) ? $student['selected'] : null }}",
                    options:{!! json_encode($student['options']) !!}
                }
            });
            var teacher = new Vue({
                el: '#teachers',
                delimiters: ['<%', '%>'],
                data: {
                    selected: "{{!empty($teacher['selected']) ? $teacher['selected'] : null }}",
                    options:{!! json_encode($teacher['options']) !!}
                }
            });

            getPlaceholder("{{empty($subject['selected'])}}", '#subject', "--请选择学科--");
            getPlaceholder("{{empty($teacher['selected'])}}", '#teacher', "--请选择老师--");
            getPlaceholder("{{empty($student['selected'])}}", '#student', "--请选择学生--");
        })
    </script>
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{$controlUrl}}/update">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{!empty($course['id']) ? $course['id'] : ''}}">
            <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
            <div class="box-body">
                <!-- body -->
                <div class="row">
                    <div class="input-group" style="width:100%; margin-bottom:20px;" id="subjects">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="subject">学科</label>
                            <select class="form-control" id="subject" name="subject" v-model="selected">
                                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;" id="students">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="student">学生</label>
                            <select class="form-control" id="student" name="student" v-model="selected">
                                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;" id="teachers">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="teacher">老师</label>
                            <select class="form-control" id="teacher" name="teacher" v-model="selected">
                                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="period">课时</label>
                            <input id="period" name="period" type="text" class="form-control" placeholder="课时" value="{{!empty($course['period']) ? $course['period'] : ''}}">
                        </div>
                    </div>
                </div>
                <div class="input-group" style="width:100%; margin-bottom:20px;">
                    <button type="submit" class="btn btn-primary" style="margin-right:20px;">提交</button>
                </div>
            </div>
        </form>
        <!-- /.search-box -->
    </div>
    <!-- /.box -->
@endsection