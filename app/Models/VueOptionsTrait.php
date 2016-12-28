<?php
namespace App\Models;

use App\Models\SubjectService;
use App\Models\TeacherService;
use Illuminate\Database\Eloquent\Model;

trait VueOptionsTrait {
    public function initVueOptions($request, &$params) {
        $this->initSex($request, $params);
        $this->initGrade($request, $params);
        $this->initTime($request, $params);
    }

    protected function initSex($request, &$params) {
        $params['vueOptions']['sex']['selected'] = $request->input('sex', '');
        $params['vueOptions']['sex']['options']  = [];
        foreach(config('language.people.sex', []) as $k => $v){
            $params['vueOptions']['sex']['options'][] = ['value' => $k, 'text' => $v];
        }
    }

    protected function initGrade($request, &$params) {
        $params['vueOptions']['grade']['selected'] = $request->input('grade', '');
        $params['vueOptions']['grade']['options']  = [];
        foreach(config('language.study.grade', []) as $k => $v){
            $params['vueOptions']['grade']['options'][] = ['value' => $k, 'text' => $v];
        }
    }

    protected function initTime($request, &$params) {
        $params['vueOptions']['openTime']['selected'] = $request->input('openTime', '');
        $params['vueOptions']['endTime']['selected'] = $request->input('endTime', '');
    }

    public function initSubjectOptions($request, &$params) {
        $params['vueOptions']['subject']['selected'] = $request->input('subject', '');
        $params['vueOptions']['subject']['options']  = [];
        $subjectService = new SubjectService();
        foreach($subjectService->getAvailable() as $v){
            $params['vueOptions']['subject']['options'][] = ['value' => $v->id, 'text' => $v->name];
        }
    }

    public function initTeacherOptions($request, &$params) {
        $params['vueOptions']['teacher']['selected'] = $request->input('teacher', '');
        $params['vueOptions']['teacher']['options']  = [];
        $teacherService = new TeacherService();
        foreach($teacherService->getAvailable() as $v){
            $params['vueOptions']['teacher']['options'][] = ['value' => $v->id, 'text' => $v->name];
        }
    }

    public function initStudentOptions($request, &$params) {
        $params['vueOptions']['student']['selected'] = $request->input('student', '');
        $params['vueOptions']['student']['options']  = [];
        $studentService = new StudentService();
        foreach($studentService->getAvailable() as $v){
            $params['vueOptions']['student']['options'][] = ['value' => $v->id, 'text' => $v->name];
        }
    }
}
