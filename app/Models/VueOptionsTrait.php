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
        $this->initType($request, $params);
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

    protected function initType($request, &$params) {
        $params['vueOptions']['type']['selected'] = $request->input('type', '');
        $params['vueOptions']['type']['options']  = [];
        foreach(config('language.type', []) as $k => $v){
            $params['vueOptions']['type']['options'][] = ['value' => $k, 'text' => $v];
        }
    }

    public function initCStatus($request, &$params) {
        $params['vueOptions']['status']['selected'] = $request->input('status', '');
        $params['vueOptions']['status']['options']  = [];
        foreach(config('language.status', []) as $k => $v){
            $params['vueOptions']['status']['options'][] = ['value' => $k, 'text' => $v];
        }
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

    public function initCourseType($request, &$params) {
        $params['vueOptions']['cType']['selected'] = $request->input('cType', '');
        $params['vueOptions']['cType']['options']  = [];
        foreach(config('language.cType', []) as $k => $v){
            $params['vueOptions']['cType']['options'][] = ['value' => $k, 'text' => $v];
        }
    }

    public function initStuGroupOptions($request, &$params, $teacher = []) {
        $params['vueOptions']['stuGroup']['selected'] = $request->input('cType', '');
        $params['vueOptions']['stuGroup']['options']  = [];
        $groupService = new GroupService();
        $subjectService = new SubjectService();
        $studentService = new StudentService();

        if(!empty($teacher)) {
            $groupList = $groupService->getListByTId($teacher);
        } else {
            $groupList = $groupService->getInfo();
        }

        foreach($groupList as $group){
            $subject = $subjectService->getNameById($group['subject']);
            $studentList = explode(',', $group['student']);
            $stuNames = [];
            foreach ($studentList as $s) {
                $stuName = $studentService->getNameById($s);
                $stuNames[] = $stuName;
            }
            $student = join(',', $stuNames);
            $cType = config("language.cType.{$group['cType']}");
            $text = $cType . "($student | $subject)";
            $params['vueOptions']['stuGroup']['options'][] = ['value' => $group['id'], 'text' => $text];
        }
    }

    public function getNameInfo($teacher = '', $subject = '', $stuStr = '', $cType = '') {
        $rea = $sub = $stu = $ct = '';
        if(!empty($teacher)) {
            $teacherService = new TeacherService();
            $rea = $teacherService->getNameById($teacher);
        }
        if(!empty($subject)) {
            $subjectService = new SubjectService();
            $sub = $subjectService->getNameById($subject);
        }
        if(!empty($stuStr)) {
            $studentService = new StudentService();
            $studentList = explode(',', $stuStr);
            $stuNames = [];
            foreach ($studentList as $student) {
                $stuName = $studentService->getNameById($student);
                $stuNames[] = $stuName;
            }
            $stu = join(',', $stuNames);
        }
        if(!empty($cType)) {
            $ct = config("language.cType.{$cType}");
        }
        return [$rea, $sub, $stu, $ct];
    }
}
