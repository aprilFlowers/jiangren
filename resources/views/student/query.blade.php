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
                    selected: "{{!empty($grade['selected'])?$grade['selected']:null}}",
                    options:{!! json_encode($grade['options']) !!}
                }
            });
            getPlaceholder("{{empty($grade['selected'])}}", '#grade', "--请选择年级--");
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
                <!-- student -->
                @if(!empty($student))
                    @foreach($student as $s)
                        <div class="box-header with-border">
                            <h3 class="box-title">学生信息</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>姓名</th>
                                    <td>{{$s['name']}}</td>
                                    <th>性别</th>
                                    <td>{{$s['sex']}}</td>
                                    <th>年龄</th>
                                    <td>{{$s['age']}}</td>
                                    <th>年级</th>
                                    <td>{{$s['grade']}}</td>
                                </tr>
                                <tr>
                                    <th>学校</th>
                                    <td>{{$s['school']}}</td>
                                    <th>联系地址</th>
                                    <td>{{$s['address']}}</td>
                                    <th>联系电话</th>
                                    <td>{{$s['phoneNum']}}</td>
                                    <th>备注(可选)</th>
                                    <td>{{$s['mark']}}</td>
                                </tr>
                            </table>
                        </div>
                        <!-- parents-->
                        <div class="box-header with-border">
                            <h3 class="box-title">家长信息</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                @if(!empty($s['family']))
                                    @foreach($s['family'] as $family)
                                        <tr>
                                            <th>家长姓名</th>
                                            <td>{{$family['parentName']}}</td>
                                            <th>联系电话</th>
                                            <td>{{$family['contactNum']}}</td>
                                            <th>工作地址</th>
                                            <td>{{$family['workAddress']}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                        <!-- course -->
                        <div class="box-header with-border">
                            <h3 class="box-title">课程信息</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                @if(!empty($s['course']))
                                    @foreach($s['course'] as $course)
                                        <tr>
                                            <th>科目</th>
                                            <td>{{$course['courseName']}}</td>
                                            <th>总课时</th>
                                            <td>{{$course['period']}}</td>
                                            <th>剩余课时</th>
                                            <td>{{$course['currentPeriod']}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>
            @endif
        </div>
        <!-- /.box-body -->
    </div>
@endsection
