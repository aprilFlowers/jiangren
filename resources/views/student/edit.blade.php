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

            function courseInfo() {
                this.course = {
                    selected : "--请选择课程--",
                    options : {!! json_encode($vueOptions['subject']['options']) !!}
                };
                this.teacher = {
                    selected : "--请选择老师--",
                    options : {!! json_encode($vueOptions['teacher']['options']) !!},
                };
                this.period = null;
                this.restPeriod = null;
            }

            var sex = new Vue({
                el: '#sexs',
                delimiters: ['<%', '%>'],
                data: {
                  selected: "{{!empty($student['baseInfos']['sex']) ? $student['baseInfos']['sex']:1}}",
                    options:{!! json_encode($vueOptions['sex']['options']) !!}
                }
            });
            var grade = new Vue({
                el: '#grades',
                delimiters: ['<%', '%>'],
                data: {
                    selected: "{{!empty($student['grade'])?$student['grade']:1}}",
                    options:{!! json_encode($vueOptions['grade']['options']) !!}
                }
            });
            var parents = new Vue({
                el: '#parents',
                delimiters: ['<%', '%>'],
                data: {
                    parents: {!! !empty($student['family']) ? json_encode($student['family']) : "[{parentName:'', contactNum:'', workAddress:''}]" !!}
                },
                methods: {
                    addParent: function () {
                        this.parents.push(new parentInfo());
                    }
                }
            });
            var studentCourses = {!! json_encode($student['courses']) !!};
            if (studentCourses.length == 0) {
              studentCourses.push(new courseInfo());
            }
            var courses = new Vue({
                el: '#courses',
                delimiters: ['<%', '%>'],
                data: {
                  subjects: {!! json_encode($vueOptions['subject']['options']) !!},
                  teachers:{!! json_encode($vueOptions['teacher']['options']) !!},
                  courses: studentCourses
                },
                methods: {
                    addCourse: function () {
                        this.courses.push(new courseInfo());
                    },
                    deleteCourse : function (index) {
                        let _courses = this.courses;
                        if(confirm('确认删除！')) {
                          if (!('id' in this.courses[index])) {
                            _courses.splice(index,1);
                          } else {
                            $.ajax({
                              url : '/student/deleteCourse',
                              dataType : 'json',
                              data :{
                                cId : this.courses[index].id
                              },
                              success : function(data) {
                                alert(data.errorMsg);
                                _courses.splice(index,1);
                              }
                            });
                          }
                        }
                    }
                }
            });

            @if(!\Entrust::can('student.enter') || !empty($_GET['preview']))
              $('.nav-tabs-custom input').attr('readonly', true);
              $('.nav-tabs-custom select').attr('disabled', true);
              $('.nav-tabs-custom button').hide();
            @else
            @endif
        })
    </script>
@endsection

@section('content')
    <form method="post" action="/student/update">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{!empty($student['id']) ? $student['id'] : ''}}">
        <input type="hidden" name="type_name"
               value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#student" data-toggle="tab">学生信息</a></li>
                <li><a href="#parents" data-toggle="tab">家长信息</a></li>
                <li><a href="#courses" data-toggle="tab">课程信息</a></li>
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
                                       value="{{!empty($student['baseInfos']['age']) ? $student['baseInfos']['age'] : ''}}">
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
                                       value="{{!empty($student['baseInfos']['school']) ? $student['baseInfos']['school'] : ''}}">
                            </div>
                        </div>
                        <div class="input-group" style="width:100%; margin-bottom:20px;">
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <label for="address">联系地址</label>
                                <input id="address" name="address" type="text" class="form-control" placeholder="联系地址"
                                       value="{{!empty($student['baseInfos']['address']) ? $student['baseInfos']['address'] : ''}}">
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
                                       value="{{!empty($student['baseInfos']['mark']) ? $student['baseInfos']['mark'] : ''}}">
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
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                      <label>家长姓名</label>
                                        <input type="text" class="form-control" placeholder="家长姓名" name="parentName[]" style="margin-bottom:10px;" v-bind:value="parent.parentName" v-model="parent.parentName">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                      <label>联系电话</label>
                                        <input type="text" class="form-control" placeholder="联系电话" name="contactNum[]" style="margin-bottom:10px;" v-bind:value="parent.contactNum" v-model="parent.contactNum">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                      <label>工作地址</label>
                                      <input type="text" class="form-control" placeholder="工作地址" name="workAddress[]" style="margin-bottom:10px;" v-bind:value="parent.workAddress" v-model="parent.workAddress">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <button type="button" class="btn btn-info" id="addParent" v-on:click="addParent">添加</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- course -->
                <div class="tab-pane" id="courses">
                  <div class="box-body">
                    <div class="form-group">
                      <div class="row" id="course">
                        <div v-for="(course, index) in courses">
                          <div class="col-lg-3 col-md-3 col-sm-12">
                            <label>科目</label>
                            <select class="subjectId form-control" name="subjects[]" v-model="course.subject">
                              <option v-for="option in subjects" v-bind:value="option.value"> <% option.text %>
                              </option>
                            </select>
                          </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <label>老师</label>
                                <select class="teacher form-control" name="teachers[]" v-model="course.teacher">
                                    <option v-for="option in teachers" v-bind:value="option.value"> <% option.text %>
                                    </option>
                                </select>
                            </div>
                          <div class="col-lg-2 col-md-2 col-sm-12">
                            <label>总课时</label>
                            <input type="text" class="form-control" placeholder="课时" name="periods[]" style="margin-bottom:10px;" v-model="course.period">
                            <input type="text" class="form-control" name="cIds[]" v-model="course.id" style="display: none;">
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-12">
                            <label>剩余课时</label>
                            <input type="text" class="form-control" placeholder="剩余课时" style="margin-bottom:10px;" v-model="course.periodLeft" readonly>
                          </div>
                            <div class="col-lg-1 col-md-1 col-sm-12">
                                <label>操作</label>
                                <button type="button" class="form-control btn btn-danger" v-on:click="deleteCourse(index)">删除</button>
                            </div>
                        </div>
                        <div class="col-lg-6">
                          <button type="button" class="btn btn-info" id="addCourse" v-on:click="addCourse">添加</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- footer button -->
            <div class="box-footer">
              <a class="btn btn-primary" href="/student/index">返回</a>
              <button type="submit" class="btn btn-primary" v-on:click="submit">提交</button>
            </div>
        </div>
        <!-- /.box -->
    </form>
@endsection
