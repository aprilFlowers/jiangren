<?php

namespace App\Http\Controllers;

use App\Models\CourseService;
use App\Models\StudentService;
use App\Models\Subject;
use App\Models\SubjectService;
use App\Models\TeacherService;
use App\Models\TimetableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Zizaco\Entrust\Entrust;

class CourseController extends Controller
{
    public function subject(Request $request) {
        $params = [];
        $params['controlUrl'] = '/course/subject';
        $this->initSearchBar($request, $params);

        $sub = new SubjectService();
        $params['allSubject'] = $sub->getSubject();

        return view('course.subject', $params);
    }

    public function subjectEdit(Request $request) {
        $id = $request->input('id', '');

        $params = [];
        $params['controlUrl'] = '/course/subject';
        $this->initSearchBar($request, $params);

        $subject = new SubjectService();
        if($id){
            $params['subject'] = $subject->getInfoById($id);
        }

        return view('course.subjectEdit', $params);
    }

    public function subjectUpdate(Request $request) {
        $id = $request->input('id');
        $data['name'] = $request->input('name');
        $data['color'] = $request->input('color');

        $subject = new SubjectService();
        if($id){
            $res = $subject->updateOne($id, $data);
        }else{
            $res = $subject->createOne($data);
        }

        $dir = "/course/subject";
        return redirect($dir);
    }

    public function subjectDelete(Request $request) {
        $id=$request->input('id', '');
        $subject = new SubjectService();
        $res = $subject->deleteOne($id);
        $dir = "/course/subject";
        return redirect($dir);
    }

    public function index(Request $request) {
        $params = [];
        $defaultData = [];
        $params['controlUrl'] = '/course/index';
        $params['admin'] = '';
        $this->initSearchBar($request, $params);

        $timetable = new TimetableService();
        $tableInfos = $timetable->getTableInfos($params['teacher']['selected'],$params['openTime']['selected'],$params['endTime']['selected'],$params['student']['selected']);
        if(!empty($tableInfos)) {
            foreach ($tableInfos as $k => $course) {
                $this->showWords($course);
                $defaultData[] = $course;
                $params['course'][] = $course;
            }
        }
        $params['course'] = $defaultData;

        if(\Entrust::hasRole(['admin'])) {
            $params['admin'] = 'admin';
        }
        return view('course.index', $params);
    }

    public function edit(Request $request) {
        $id = $request->input('id', '');

        $params = [];
        $params['controlUrl'] = '/course/index';
        $this->initSearchBar($request, $params);

        if($id){
            $course = new CourseService();
            $courseInfos = $course->getInfoById($id);
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
        $couService = new CourseService();
        $hisCourseData = $couService->getInfoById($id);
        $res = $couService->deleteOne($id);
        $stuService = new StudentService();
        $stuInfos = $stuService->getInfoById($hisCourseData['student']);
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
        $r = $stuService->updateOne($hisCourseData['student'], $coursesData);
        $dir = "/course/index";
        return redirect($dir);
    }

    public function clickCourse(Request $request) {
        $msg = [];
        $cid = $request->input('cid');
        $sid = $request->input('sid');
        $timeTable = new TimetableService();
        $stu = new StudentService();
        $studentInfos = $stu->getInfoById($sid);
        if(!empty($studentInfos)) {
            $courseInfos = $studentInfos['courseInfos'];
            $courseInfos = json_decode($courseInfos, true);
            $data = [];
            foreach ($courseInfos as $k => $v) {
                // update student course infos
                if ($v['courseId'] == $cid) {
                    $data[] = [
                        'courseId' => $v['courseId'],
                        'lastPeriod' => $v['lastPeriod'] + 2,
                    ];
                } else {
                    $data[] = $v;
                }
            }
            $datas = json_encode($data);
            $r = $stu->updateCoursePeriod($sid, $datas);
            if ($r) {
                // update course status
                $res = $timeTable->updateCourseStatus($cid, 1);
                $msg['errorMsg'] = $res ? '操作成功！' : '操作失败！';
            } else {
                $msg['errorMsg'] = '操作失败！';
            }
        }
        echo json_encode($msg);
    }

    public function timetable(Request $request) {
        $params = [];
        $params['admin'] = '';
        $params['controlUrl'] = '/course/timetable';
        $this->initSearchBar($request, $params);
        $tId = $request->input('teacher');
        $sId = $request->input('student');
        $week = $request->input('week');
        $date = date('Y-m-d');
        $first = 1;

        $course = new CourseService();
        $timetable = new TimetableService();
        if($request['_token']) {
            $date = $week ? date('Y-m-d', strtotime($week)) : $date;
        }

        $courseInfos = $course->getCoursesInfos($tId, $sId);

        $w = date('w', strtotime($date));
        $week_start = date('Y-m-d', strtotime("$date -".($w ? $w-$first : 6).'days'));
        $week_end = date('Y-m-d', strtotime("$week_start +6days"));

        $tableInfos = $timetable->getTableInfos($tId, $week_start, $week_end, $sId);
        $params['time'] = $week_start.'  --  '.$week_end;

        $this->formatTableData($tableInfos, $courseInfos, $params);

        if(\Entrust::hasRole(['admin'])) {
            $params['admin'] = 'admin';
        }
        return view('course.timetable', $params);
    }

    public function saveTimetableData(Request $request) {
        $msg = [];
        $id = $request->input('id');
        $index = $request->input('index');
        $content = $request->input('content');
        $teacher = new TeacherService();
        $student = new StudentService();
        $course = new CourseService();
        $subject = new SubjectService();

        $section = substr($index, 0, 1);
        $courseInfos = explode('|', $content);
        $cId = $subject->getIdByName(trim($courseInfos[0]));
        $tId = $teacher->getIdByName(trim($courseInfos[1]));
        $sId = $student->getIdByName(trim($courseInfos[2]));
        $data = [
            'subject' => $cId[0],
            'teacher' => $tId[0],
            'student' => $sId[0],
            'index' => $index,
            'status' => 0,
        ];
        $sectionTime = config("language.section.$section");
        $start = $sectionTime['start'];
        $end = $sectionTime['end'];
        $default = date('Y-m-d');
        $data['start'] = $default.' '.$start;
        $data['end'] = $default.' '.$end;

        $timetable = new TimetableService();
        if(empty($id)) {
            $res = $timetable->createOne($data);
        }else {
            $res = $timetable->updateOne($id, $data);
        }

        $msg['id'] = $res ? $res : -1;
        $msg['errorMsg'] = $res ? '操作成功' : '操作失败！';
        return json_encode($msg);
    }

    public function deleteTimetableData(Request $request) {
        $msg = [];
        $id = $request->input('id');

        $timetable = new TimetableService();
        $res = $timetable->deleteOne($id);

        $msg['errorMsg'] = $res ? '操作成功' : '操作失败！';
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
        $cou = new CourseService();
        $stu = new StudentService();
        $tea = new TeacherService();
        $sub = new SubjectService();
        if(!empty($course['student'])) {
            $studentName = $stu->getNameById($course['student']);
            if (!empty($studentName)) {
                $course['studentName'] = $studentName[0];
            }
        }
        if(!empty($course['teacher'])) {
            $teacherName = $tea->getNameById($course['teacher']);
            if (!empty($teacherName)) {
                $course['teacherName'] = $teacherName[0];
            }
        }
        if(!empty($course['course'])) {
            $subject = $cou->getSubjectById($course['course']);
            if(!empty($subject)) {
                $courseName = $sub->getNameById($subject[0]);
            }
        }
        if(!empty($course['subject'])) {
            $courseObj = $sub->getInfoById($course['subject']);
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
        $params['subject']['options']  = [];
        $subject = new SubjectService();
        $allSubject = $subject->getSubject();
        foreach($allSubject as $k => $subject){
            $params['subject']['options'][] = ['value' => $subject['id'], 'text' => $subject['name']];
        }
        $params['teacher']['selected'] = '-1';
        $params['teacher']['options']  = [];
        $params['teacher']['options'][] = ['value' => -1, 'text' => '全部老师'];
        $teacher = new TeacherService();
        $allTeachers = $teacher->getTeachers();
        foreach($allTeachers as $k => $teacher){
            $params['teacher']['options'][] = ['value' => $teacher['id'], 'text' => $teacher['name']];
        }
        $params['student']['selected'] = '-1';
        $params['student']['options']  = [];
        $params['student']['options'][] = ['value' => -1, 'text' => '全部学生'];
        $student = new StudentService();
        $allStudents = $student->getStudents();
        foreach($allStudents as $k => $student){
            $params['student']['options'][] = ['value' => $student['id'], 'text' => $student['name']];
        }
        $params['section']['selected'] = '';
        $params['section']['options']  = [];
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
