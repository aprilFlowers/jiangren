@extends('layouts.app')

@section('js')
    <script src="/public/default/js/vue.js"></script>
    <script src="/public/default/js/common/selectPlaceholder.js"></script>
    <script>
        $(function () {
            function parentInfo() {
                this.parentName = null;
                this.contactNum = null;
                this.workAddress = null;
            }

            var sex = new Vue({
                el: '#sexs',
                delimiters: ['<%', '%>'],
                data: {
                    selected: "{{!empty($sex['selected']) ? $sex['selected'] : null }}",
                    options:{!! json_encode($sex['options']) !!}
                }
            });
            var grade = new Vue({
                el: '#grades',
                delimiters: ['<%', '%>'],
                data: {
                    selected: "{{!empty($grade['selected'])?$grade['selected']:null}}",
                    options:{!! json_encode($grade['options']) !!}
                }
            });
            var parent = new Vue({
                el: '#parents',
                delimiters: ['<%', '%>'],
                data: {
                    parents: {!! !empty($student['family']) ? json_encode($student['family']) : $students['familyDefault'] !!}
                },
                methods: {
                    addParent: function () {
                        var newParent = new parentInfo();
                        this.parents.push(newParent);
                    }
                }
            });

            getPlaceholder("{{empty($sex['selected'])}}", '#sex', "--请选择性别--");
            getPlaceholder("{{empty($grade['selected'])}}", '#grade', "--请选择年级--");
            $(".select2").select2();
        })
    </script>
@endsection

@section('content')
    <form method="post" action="{{$controlUrl}}/update">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{!empty($student['id']) ? $student['id'] : ''}}">
        <input type="hidden" name="type_name"
               value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#student" data-toggle="tab">学生信息</a></li>
                <li><a href="#parents" data-toggle="tab">家长信息</a></li>
                <li><a href="#course" data-toggle="tab">课程信息</a></li>
            </ul>
            <!-- nav-tabs-custom -->
            <div class="tab-content">
                <!-- student -->
                <div class="tab-pane active" id="student">
                    <div class="box-body">
                        <div class="input-group" style="width:100%; margin-bottom:20px;">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="name">姓名</label>
                                <input id="name" name="name" type="text" class="form-control" placeholder="姓名"
                                       value="{{!empty($student['name']) ? $student['name'] : ''}}">
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;" id="sexs">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="sex">性别</label>
                                <select class="form-control" id="sex" name="sex" v-model="selected">
                                    <option v-for="option in options" v-bind:value="option.value"> <% option.text %>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="age">年龄</label>
                                <input id="age" name="age" type="text" class="form-control" placeholder="年龄"
                                       value="{{!empty($student['age']) ? $student['age'] : ''}}">
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;" id="grades">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="grade">年级</label>
                                <select class="form-control" id="grade" name="grade" v-model="selected">
                                    <option v-for="option in options" v-bind:value="option.value"> <% option.text %>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="school">学校</label>
                                <input id="school" name="school" type="text" class="form-control" placeholder="学校"
                                       value="{{!empty($student['school']) ? $student['school'] : ''}}">
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="address">联系地址</label>
                                <input id="address" name="address" type="text" class="form-control" placeholder="联系地址"
                                       value="{{!empty($student['address']) ? $student['address'] : ''}}">
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="phoneNum">联系电话</label>
                                <input id="phoneNum" name="phoneNum" type="text" class="form-control" placeholder="联系电话"
                                       value="{{!empty($student['phoneNum']) ? $student['phoneNum'] : ''}}">
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="mark">备注</label>
                                <input id="mark" name="mark" type="text" class="form-control" placeholder="备注"
                                       value="{{!empty($student['mark']) ? $student['mark'] : ''}}">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- parents -->
                <div class="tab-pane" id="parents">
                    <div class="box-body">
                        <div class="form-group">
                            <div class="row" id="family">
                                <div v-for="parent in parents">
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" placeholder="家长姓名" name="parentName[]" style="margin-bottom:10px;"
                                               v-bind:value="parent.parentName" v-model="parent.parentName">
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" placeholder="联系电话" name="contactNum[]" style="margin-bottom:10px;"
                                               v-bind:value="parent.contactNum" v-model="parent.contactNum">
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <input type="text" class="form-control" placeholder="工作地址" name="workAddress[]" style="margin-bottom:10px;"
                                               v-bind:value="parent.workAddress" v-model="parent.workAddress">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <a class="btn btn-info" id="addParent" v-on:click="addParent">添加</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- course -->
                <div class="tab-pane" id="course">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="course">科目</label>
                            <select class="form-control select2" multiple="multiple" data-placeholder="--请选择科目--" style="width: 100%;" name="course[]">
                                @foreach($courses as $key=>$course)
                                    @if(!empty($student['course']))
                                        <option value="{{$course['id']}}" {{(in_array($course['id'], $student['course']))? 'selected':''}}>{{$course['name']}}</option>
                                    @else
                                        <option value="{{$course['id']}}">{{$course['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- footer button -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary" style="margin-right:20px;" v-on:click="submit">提交</button>
            </div>
        </div>
        <!-- /.box -->
    </form>
@endsection