<?php

namespace App\Http\Controllers;

use App\Models\CourseService;
use App\Models\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request) {
        $params = [];
        $params['controlUrl'] = '/teacher/index';
        $this->initSearchBar($request, $params);

        $teacher = new TeacherService();
        $params['teachers'] = $teacher->getTeachers();

        return view('teacher.index', $params);
    }

    public function edit(Request $request) {
        $id = $request->input('id', '');

        $params = [];
        $params['controlUrl'] = '/teacher';
        $this->initSearchBar($request, $params);

        if($id){
            $teacher = new TeacherService();
            $params['teacher'] = $teacher->getInfoById($id);
            $params['sex']['selected'] = $params['teacher']['sex'];
        }

        return view('teacher.edit', $params);
    }

    public function update(Request $request) {
        $id = $request->input('id', '');
        $keys = ['name', 'sex', 'phoneNum'];
        foreach ($keys as $key){
            $data[$key] = $request->input($key, '');
        }

        if($id){
            $teacher = new TeacherService();
            $res = $teacher->updateOne($id, $data);
            $dir = "/teacher/index";
        }else{
            $teacher = new TeacherService();
            $res = $teacher->createOne($data);
            $dir = "/teacher/index";
        }

        return redirect($dir);
    }

    public function delete(Request $request) {
        $id=$request->input('id', '');
        $teacher = new TeacherService();
        $res = $teacher->deleteOne($id);
        $course = new CourseService();
        if($res) {
            $res = $course->deleteCourseByTeacher($id);
        }
        $dir = "/teacher/index";
        return redirect($dir);
    }

    protected function initSearchBar($request, &$params) {
        $params['sex']['selected'] = '';
        $params['sex']['options']  = [];
        $sexCof = config('language.sex');
        foreach($sexCof as $k => $sex){
            $params['sex']['options'][] = ['value' => $k, 'text' => $sex];
        }
    }
}
