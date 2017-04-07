<?php
namespace App\Models;

use App\Models\SubjectService;
use App\Models\TeacherService;
use Illuminate\Database\Eloquent\Model;

use Auth;
use Entrust;

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

    // get all: status = null
    public function initSubjectOptions($request, &$params, $status = 1) {
        $params['vueOptions']['subject']['selected'] = $request->input('subject', '');
        $params['vueOptions']['subject']['options']  = [];
        $subjectService = new SubjectService();
        $query = [];
        if (!is_null($status)) {
            $query['status'] = $status;
        }
        foreach($subjectService->getInfoByQuery($query) as $v){
            $params['vueOptions']['subject']['options'][] = ['value' => $v->id, 'text' => $v->name];
        }
    }

    // get all: status = null
    public function initTeacherOptions($request, &$params, $status = 1, $allOptions = false) {
        $params['vueOptions']['teacher']['selected'] = $request->input('teacher', '');
        $params['vueOptions']['teacher']['options']  = [];
        $teacherService = new TeacherService();
        $query = [];
        if (!is_null($status)) {
            $query['status'] = $status;
        }
        foreach($teacherService->getInfoByQuery($query) as $v){
            if ($allOptions || Entrust::hasRole('admin') || Entrust::hasRole('student')) {
                $params['vueOptions']['teacher']['options'][] = ['value' => $v->id, 'text' => $v->name];
            } elseif (Entrust::hasRole('teacher') && Auth::user()['id'] == $v->userId) {
                $params['vueOptions']['teacher']['selected'] = $v->id;
                $params['vueOptions']['teacher']['options'][] = ['value' => $v->id, 'text' => $v->name];
            }
        }
    }

    // get all: status = null
    public function initStudentOptions($request, &$params, $status = 1, $allOptions = false) {
        $params['vueOptions']['student']['selected'] = $request->input('student', '');
        $params['vueOptions']['student']['options']  = [];
        $studentService = new StudentService();
        $query = [];
        if (!is_null($status)) {
            $query['status'] = $status;
        }
        foreach($studentService->getInfoByQuery($query) as $v){
            if ($allOptions || Entrust::hasRole('admin') || Entrust::hasRole('teacher')) {
                $params['vueOptions']['student']['options'][] = ['value' => $v->id, 'text' => $v->name];
            } elseif (Entrust::hasRole('student') && Auth::user()['id'] == $v->userId) {
                $params['vueOptions']['student']['selected'] = $v->id;
                $params['vueOptions']['student']['options'][] = ['value' => $v->id, 'text' => $v->name];
            }
        }
    }
}
