@extends('layouts.app')

@section('js')
  <script src="/public/default/js/vue.js"></script>
  <script src="/public/default/js/common/selectPlaceholder.js"></script>
  <script>
    $(function(){
      $(".select2").select2();
      new Vue({
        el: '#teachers',
        delimiters: ['<%','%>'],
        data: {
          selected: "{{!empty($group['teacher']) ? $group['teacher'] : '' }}",
          options:{!! json_encode(array_replace($vueOptions['teacher']['options'])) !!}
        }
      });
      new Vue({
        el: '#subjects',
        delimiters: ['<%','%>'],
        data: {
          selected: "{{!empty($group['subject']) ? $group['subject'] : 1 }}",
          options:{!! json_encode(array_replace($vueOptions['subject']['options'])) !!}
        }
      });
      new Vue({
        el: '#cTypes',
        delimiters: ['<%','%>'],
        data: {
          selected: "{{!empty($group['cType']) ? $group['cType'] : 1 }}",
          options:{!! json_encode(array_replace($vueOptions['cType']['options'])) !!}
        }
      });
      getPlaceholder("{{empty($group['teacher'])}}", '#teacher', '请选择老师');
    })
  </script>
@endsection

@section('content')
  <div class="box box-primary">
    <form method="post" action="{{$controlUrl}}/update">
      {{ csrf_field() }}
      <input type="hidden" name="id" value="{{!empty($group['id']) ? $group['id'] : ''}}">
      <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
      <div class="box-body">
        <!-- body -->
        <div class="row">
          <div class="input-group" style="width:100%; margin-bottom:20px;" id="teachers">
            <div class="col-xs-12 col-md-6 col-lg-4">
              <label for="teacher">老师</label>
              <select class="form-control" id="teacher" name="teacher" v-model="selected">
                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
              </select>
            </div>
          </div>
          <div class="input-group" style="width:100%; margin-bottom:20px;">
            <div class="col-xs-12 col-md-6 col-lg-4" id="subjects">
              <label for="subject">课程</label>
              <select class="form-control" id="subject" name="subject" v-model="selected">
                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
              </select>
            </div>
          </div>
          <div class="input-group" style="width:100%; margin-bottom:20px;" id="cTypes">
            <div class="col-xs-12 col-md-6 col-lg-4">
              <label for="cType">课程形式</label>
              <select class="form-control" id="cType" name="cType" v-model="selected">
                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
              </select>
            </div>
          </div>
          <div class="input-group" style="width:100%; margin-bottom:20px;" id="students">
            <div class="col-xs-12 col-md-6 col-lg-4">
              <label for="student">学生</label>
              <select class="form-control select2" multiple="multiple" data-placeholder="请选择学生" style="width: 100%;" name="student[]">
                @foreach($vueOptions['student']['options'] as $student)
                  @if (empty($group['student']))
                    <option value="{{$student['value']}}">{{$student['text']}}</option>
                  @else
                    <option value="{{$student['value']}}" {{(in_array($student['value'], $group['student']))? 'selected':''}}>{{$student['text']}}</option>
                  @endif
                @endforeach
              </select>
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
