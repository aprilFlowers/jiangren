<?php

namespace App\Http\Controllers;

use App\Models\StudentService;
use App\Models\SubjectService;
use App\Models\CourseService;
use App\Models\TeacherService;
use App\Models\TimetableService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->subjectService = new SubjectService();
        $this->courseService = new CourseService();
        $this->timetableService = new TimetableService();
        $this->studentService = new StudentService();
        $this->teacherService = new TeacherService();
    }

    public function subject(Request $request) {
        $params = [];
        $params['subjects'] = $this->subjectService->getAvailable();
        return view('course.subject', $params);
    }

    public function subjectEdit(Request $request) {
        $params = [];
        $this->initVueOptions($request, $params);
        // data
        if($id = $request->input('id')){
            $params['subject'] = $this->subjectService->getInfoById($id);
        }
        return view('course.subjectEdit', $params);
    }

    public function subjectUpdate(Request $request) {
        $data = [
            'name'  => $request->input('name', ''),
            'color' => $request->input('color', ''),
        ];
        if($id = $request->input('id')){
            $res = $this->subjectService->updateOne($id, $data);
        }else{
            $res = $this->subjectService->createOne($data);
        }
        return redirect("/course/subject");
    }

    public function subjectDelete(Request $request) {
        if($id = $request->input('id')) {
            // disable
            $this->subjectService->updateOne($id, ['status' => 0]);
        }
        return redirect("/course/subject");
    }

    public function index(Request $request) {
        $params = [];
        $this->initVueOptions($request, $params);
        $this->initStudentOptions($request, $params);
        $this->initTeacherOptions($request, $params);

        $courses = $this->courseService->getInfoByQuery([
            'teacher' => $request->input("teacher", ''),
            'student' => $request->input("student", ''),
        ]);
        $courseIds = [];
        foreach ($courses as $course) {
            $courseIds[] = $course['id'];
        }
        $tableInfos = $this->timetableService->getInfoByQuery([
            'course' => ['in', $courseIds],
            'start' => ['>=', $request->input("openTime", '')],
            'end' => ['<=', $request->input("endTime", '')],
        ]);
        foreach ($tableInfos as &$t) {
            if (!empty($courses[$t['course']])) {
                $t['courseInfo'] = $courses[$t['course']];
            }
        }
        $params['course'] = $tableInfos;
        $params['admin'] = \Entrust::hasRole(['admin']) ? 'admin' : '';
        return view('course.index', $params);
    }

    public function edit(Request $request) {
        $id = $request->input('id', '');

        $params = [];
        $this->initVueOptions($request, $params);

        if($id){
            $courseInfos = $this->courseService->getInfoById($id);
            $keys = ['teacher', 'student', 'subject'];
            foreach ($keys as $key){
                $params[$key]['selected'] = $courseInfos[$key];
            }
            $_keys = ['id', 'period'];
            foreach ($_keys as $_key){
                $params['course'][$_key] = $courseInfos[$_key];
            }
        }

        return view('course.edit', $params);
    }

    public function delete(Request $request) {
        $id=$request->input('id', '');
        $hisCourseData = $this->courseService->getInfoById($id);
        $res = $this->courseService->deleteOne($id);
        $stuInfos = $this->studentService->getInfoById($hisCourseData['student']);
        $courseInfos = json_decode($stuInfos['courseInfos'], true);

        $_data = [];
        if(!empty($courseInfos)) {
            foreach ($courseInfos as $k => $v) {
                if ($v['courseId'] != $id) {
                    $_data[] = $v;
                }
            }
        }
        $coursesData['courseInfos'] = json_encode($_data);
        $r = $this->studentService->updateOne($hisCourseData['student'], $coursesData);
        $dir = "/course/index";
        return redirect($dir);
    }

    public function clickCourse(Request $request) {
        $cid = $request->input('cid');

        $msg = [ 'errorCode' => 1, 'errorMsg' => '操作失败！' ];

        $res = $this->timetableService->updateOne($cid, ['status' => 2]);
        if ($res) {
            $msg['errorCode'] = 0;
            $msg['errorMsg'] = '操作成功';
        }
        echo json_encode($msg);
    }

    protected function getWeekStartEnd($time = null) {
        $date = $time ? date('Y-m-d', strtotime($time)) : date('Y-m-d');
        $w = date('w', strtotime($date));
        $weekStart = date('Y-m-d', strtotime("$date -".($w ? $w-1: 6).' days'));
        $weekEnd = date('Y-m-d', strtotime("$weekStart +6 days"));
        return [$weekStart, $weekEnd];
    }

    public function timetable(Request $request) {
        $params = [];
        $this->initVueOptions($request, $params);
        $this->initStudentOptions($request, $params);
        $this->initTeacherOptions($request, $params);

        // courses
        $courses = $this->courseService->getInfoByQuery([
            'teacher' => $request->input("teacher", ''),
            'student' => $request->input("student", ''),
        ]);
        $params['courses'] = $courses;
        // timetable
        $courseIds = [];
        foreach ($courses as $course) {
            $courseIds[] = $course['id'];
        }
        list($weekStart, $weekEnd) = $this->getWeekStartEnd($request->input('openTime'));
        $table = $this->timetableService->getInfoByQuery([
            'course' => ['in', $courseIds],
            'start' => ['>=', $weekStart],
            'end' => ['<=', $weekEnd.' 23:59:59'],
            'status' => ['in', [1, 2]], // active or confirmed
        ]);
        foreach ($table as &$t) {
            if (!empty($courses[$t['course']])) {
                $t['courseInfo'] = $courses[$t['course']];
            }
        }
        $params['table'] = $table;
        // others
        $params['time'] = $weekStart.'  --  '.$weekEnd;
        $params['weekStart'] = $weekStart;
        $params['weekEnd'] = $weekEnd;
        $params['lessons'] = config('language.study.lesson', []);
        $params['admin'] = \Entrust::hasRole(['admin']) ? 'admin' : '';
        return view('course.timetable', $params);
    }

    public function saveTimetableData(Request $request) {
        $id = $request->input('id');
        $index = $request->input('index');
        $courseId = $request->input('courseId');
        $weekStart = $request->input('weekStart');
        $weekEnd = $request->input('weekEnd');

        $msg = [ 'errorCode' => 1, 'errorMsg' => '操作失败！' ];

        // get existing table
        if (!empty($id)) {
            $table = $this->timetableService->getInfoById($id);
            // error
            if (empty($table)) {
                $msg['errorMsg'] = '没有找到该排课';
                return json_encode($msg);
            }
            // get course info
            $courseId = $table['course'];
        }

        // get course
        if (!empty($courseId)) {
            $course = $this->courseService->getInfoById($courseId);
        }
        // error
        if (empty($course)) {
            $msg['errorMsg'] = '没有找到该课程';
            return json_encode($msg);
        }

        $table = $this->timetableService->getInfoByQuery([
            'course' => $course['id'],
            'start' => ['>=', $weekStart],
            'end' => ['<=', $weekEnd.' 23:59:59'],
            'index' => $index,
        ]);

        if (!empty($table) && count($table) > 0) {
            $msg['errorMsg'] = '该课程已添加，不需要重复添加';
            return json_encode($msg);
        }

        // add base
        $data = [
            'course' => $course['id'],
            'index' => $index,
        ];
        // add time
        $lesson = substr($index, 0, 1);
        $weekDay = substr($index, 1, 1);
        $lessonTime = config("language.study.lesson.$lesson");
        $start = $lessonTime['start'];
        $end = $lessonTime['end'];
        list($weekStart, $weekEnd) = $this->getWeekStartEnd();
        $date = date('Y-m-d', strtotime("$weekEnd -".(7-$weekDay).' days'));
        $data['start'] = $date.' '.$start;
        $data['end'] = $date.' '.$end;
        // create or update
        if(empty($id)) {
            $res = $this->timetableService->createOne($data);
        }else {
            $res = $this->timetableService->updateOne($id, $data);
        }

        // response
        if ($res) {
            $msg['errorCode'] = 0;
            $msg['errorMsg'] = '操作成功';
            $msg['data'] = ['id' => $res];
        }
        return json_encode($msg);
    }

    public function deleteTimetableData(Request $request) {
        $id = $request->input('id');

        $res = $this->timetableService->deleteOne($id);

        $msg = [ 'errorCode' => 1, 'errorMsg' => '操作失败！' ];
        if ($res) {
            $msg = [ 'errorCode' => 0, 'errorMsg' => '操作成功' ];
        }
        return json_encode($msg);
    }

    protected function formatTableData($tableInfos, $courseInfos, &$params) {
        $params['table'] = [];
        $params['courseDragable'] = [];
        if(!empty($courseInfos)) {
            foreach ($courseInfos as $courseInfo) {
                $this->showWords($courseInfo);
                $params['courseDragable'][] = [
                    'id' => $courseInfo['id'],
                    'courseName' => $courseInfo['courseName'],
                    'teacher' => $courseInfo['teacherName'],
                    'student' => $courseInfo['studentName'],
                    'color' => $courseInfo['courseColor'],
                ];
            }
        }
        if(!empty($tableInfos)) {
            foreach ($tableInfos as $tableInfo) {
                $this->showWords($tableInfo);
                $content = $tableInfo['courseName']. ' | ' .$tableInfo['teacherName']. ' | ' .$tableInfo['studentName'];

                $data = [
                    'id' => $tableInfo['id'],
                    'status' => $tableInfo['status'],
                    'content' => $content,
                    'color' => $tableInfo['courseColor'],
                ];
                if(!empty($params['table']) && in_array($tableInfo['index'], array_keys($params['table']))) {
                    $params['table'][$tableInfo['index']][] = $data;
                }else {
                    $index = $tableInfo['index'];
                    $params['table'][$index][] = $data;
                }
            }
        }
    }

    protected function showWords(&$course) {
        if(!empty($course['student'])) {
            $studentName = $this->studentService->getNameById($course['student']);
            if (!empty($studentName)) {
                $course['studentName'] = $studentName[0];
            }
        }
        if(!empty($course['teacher'])) {
            $teacherName = $this->teacherService->getNameById($course['teacher']);
            if (!empty($teacherName)) {
                $course['teacherName'] = $teacherName[0];
            }
        }
        if(!empty($course['course'])) {
            $subject = $this->courseService->getSubjectById($course['course']);
            if(!empty($subject)) {
                $courseName = $this->subjectService->getNameById($subject[0]);
            }
        }
        if(!empty($course['subject'])) {
            $courseObj = $this->subjectService->getInfoById($course['subject']);
            if (!empty($courseObj)) {
                $courseName = $courseObj->name;
                $courseColor = $courseObj->color;
            }
        }
        if (!empty($courseName)) {
            $course['courseName'] = $courseName;
        }
        if (!empty($courseColor)) {
            $course['courseColor'] = $courseColor;
        }
    }

    protected function initSearchBar($request, &$params) {
        $params['subject']['selected'] = '';
        $allSubject = $this->subjectService->getSubject();
        foreach($allSubject as $k => $subject){
            $params['subject']['options'][] = ['value' => $subject['id'], 'text' => $subject['name']];
        }
        $params['teacher']['selected'] = '-1';
        $params['teacher']['options'][] = ['value' => -1, 'text' => '全部老师'];
        $allTeachers = $this->teacherService->getTeachers();
        foreach($allTeachers as $k => $teacher){
            $params['teacher']['options'][] = ['value' => $teacher['id'], 'text' => $teacher['name']];
        }
        $params['student']['selected'] = '-1';
        $params['student']['options'][] = ['value' => -1, 'text' => '全部学生'];
        $allStudents = $this->studentService->getStudents();
        foreach($allStudents as $k => $student){
            $params['student']['options'][] = ['value' => $student['id'], 'text' => $student['name']];
        }
        $params['section']['selected'] = '';
        $allSections = config('language.section');
        foreach($allSections as $k => $section){
            $params['section']['options'][] = ['value' => $k, 'text' => $section['name']];
        }
        $params['sectionList'] = $allSections;

        $params['week']['selected'] = date('Y-m-d');
        $params['openTime']['selected'] = date('Y-m-d H:i:s', strtotime('-14 day'));
        $params['endTime']['selected'] = date('Y-m-d H:i:s');

        if($request['_token']){
            $params['_token'] = $request['_token'];
            if(!empty($name = $request->input('name'))){
                $params['name']= $name;
            }
            if(!empty($teacher= $request->input('teacher'))){
                $params['teacher']['selected'] = $teacher;
            }
            if(!empty($student= $request->input('student'))){
                $params['student']['selected'] = $student;
            }
            if(!empty($openTime = $request->input('openTime'))){
                $params['openTime']['selected'] = $openTime;
            }
            if(!empty($endTime = $request->input('endTime'))){
                $params['endTime']['selected'] = $endTime;
            }
            if(!empty($section = $request->input('section'))){
                $params['section']['selected'] = $section;
            }
            if(!empty($week = $request->input('week'))){
                $params['week']['selected'] = $week;
            }
        }
    }
}
