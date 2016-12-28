@extends('layouts.app')

@section('js')
    <script src="/public/default/js/vue.js"></script>
    <script src="/public/default/js/common/selectPlaceholder.js"></script>
    <script>
        $(function(){
            var sex = new Vue({
                el: '#sexs',
                delimiters: ['<%','%>'],
                data: {
                    selected: "{{!empty($teacher['sex']) ? $teacher['sex'] : 1 }}",
                    options:{!! json_encode(array_replace($vueOptions['sex']['options'])) !!}
                }
            });
            //sex.options.unshift({'value':-1, 'text':'无性别'});
        })
    </script>
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="/teacher/update">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{!empty($teacher['id']) ? $teacher['id'] : ''}}">
            <input type="hidden" name="type_name" value="{{!empty($globalBreadcrumb) ? $globalBreadcrumb[count($globalBreadcrumb)-1]['name'] : ''}}">
            <div class="box-body">
                <!-- body -->
                <div class="row">
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="name">姓名</label>
                            <input id="name" name="name" type="text" class="form-control" placeholder="姓名" value="{{!empty($teacher['name']) ? $teacher['name'] : ''}}">
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;" id="sexs">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="sex">性别</label>
                            <select class="form-control" id="sex" name="sex" v-model="selected">
                                <option v-for="option in options" v-bind:value="option.value"> <% option.text %> </option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group" style="width:100%; margin-bottom:20px;">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <label for="phoneNum">电话</label>
                            <input id="phoneNum" name="phoneNum" type="text" class="form-control" placeholder="电话" value="{{!empty($teacher['phoneNum']) ? $teacher['phoneNum'] : ''}}">
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
