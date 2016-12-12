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
        $tableInfos = $timetable->getTableInfos();
        if(!empty($tableInfos)) {
            foreach ($tableInfos as $k => $course) {
                $this->showWords($course);
                $course['status'] = $course['status'] ? '已确认' : '未确认';
                $defaultData[] = $course;

            }
        }
        $params['course'] = $defaultData;

        $keys = ['student', 'teacher', 'openTime', 'endTime'];
        foreach ($keys as $key){
            if (in_array($key, ['openTime', 'endTime'])) {
                $data[$key] = $request->input($key) ? date('Y-m-d H:i:s', strtotime($request->input($key))) : '';
            }else {
                $data[$key] = $request->input($key, '');
            }
        }
        $timetable = new TimetableService();
        if($request['_token']) {
            $params['course'] = [];
            $courseInfos = $timetable->getTableInfos($data['teacher'], $data['openTime'], $data['endTime'], $data['student']);
            if(!empty($courseInfos)) {
                foreach ($courseInfos as $courseInfo) {
                    $course = $courseInfo;
                    $this->showWords($course);
                    $params['course'][] = $course;
                }
            }
        }
        if(\Entrust::hasRole(['admin'])) {
            $params['admin'] = 'admin';
        }
        return view('course.query', $params);
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

    public function update(Request $request) {
        $id = $request->input('id');
        $keys = ['subject', 'student', 'teacher', 'period'];
        foreach ($keys as $key){
            $data[$key] = $request->input($key, '');
        }
        $couService = new CourseService();
//        $stuService = new StudentService();
//        $hisCourseData = $couService->getInfoById($id);
//        $hisPeriod = $hisCourseData['period'];

        if($id){
            $res = $couService->updateOne($id, $data);
        }else{
            $data['status'] = 0;
            $res = $couService->createOne($data);
        }
        // update student courseInfos
//        $_data = [];
//        $stuInfos = $stuService->getInfoById($data['student']);
//        $courseInfos = json_decode($stuInfos['courseInfos'], true);
//
//        if($courseInfos) {
//            foreach ($courseInfos as $course => $period) {
//                $_data[$course] = $period;
//                if ($course == $res) {
//                    $_data[$course] = $data['period'] - ($hisPeriod - $period);
//                }
//            }
//        }
//        if (!in_array($res, array_keys($courseInfos))) {
//            $_data[$res] = $data['period'];
//        }
//        $coursesData['courseInfos'] = json_encode($_data);
//        $r = $stuService->updateOne($data['student'], $coursesData);
//        $dir = "/course/index";
//
//        return redirect($dir);
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
            foreach ($courseInfos as $course => $period) {
                if ($course != $id) {
                    $_data[$course] = $period;
                }
            }
        }
        $coursesData['courseInfos'] = json_encode($_data);
        $r = $stuService->updateOne($hisCourseData['student'], $coursesData);
        $dir = "/course/index";
        return redirect($dir);
    }

    public function clickCourse(Request $request) {
        $cid = $request->input('cid');
        $sid = $request->input('sid');
        $cour = new CourseService();
        $stu = new StudentService();
        $studentInfos = $stu->getInfoById($sid);
        $courseInfos = $studentInfos['courseInfos'];
        $courseInfos = json_decode($courseInfos, true);
        foreach ($courseInfos as $c => $p) {
            if ($p < 2) {
                $msg['errorMsg'] = '该课程已确认！';
                echo json_encode($msg);exit;
            }
            if($c == $cid) {
                $p = $p - 2;
            }
            $data[$c] = $p;
        }
        $datas = json_encode($data);
        $r = $stu->updateCoursePeriod($sid, $datas);
        if($r) {
            $res = $cour->updateCourseStatus($cid);
            $msg['errorMsg'] = $res ? '操作成功！' : '操作失败！';
            echo json_encode($msg);exit;
        }
        $msg['errorMsg'] = '操作失败！';
        echo json_encode($msg);
    }

    public function timetable(Request $request) {
        $params = [];
        $params['admin'] = '';
        $params['controlUrl'] = '/course/timetable';
        $this->initSearchBar($request, $params);
        $tId = $request->input('teacher');
        $week = $request->input('week');
        $date = date('Y-m-d');
        $first = 1;

        $course = new CourseService();
        $timetable = new TimetableService();
        if($request['_token']) {
            $date = $week ? date('Y-m-d', strtotime($week)) : $date;
        }

        $courseInfos = $course->getCourses();

        $w = date('w', strtotime($date));
        $week_start = date('Y-m-d', strtotime("$date -".($w ? $w-$first : 6).'days'));
        $week_end = date('Y-m-d', strtotime("$week_start +6days"));

        $tableInfos = $timetable->getTableInfos($tId, $week_start, $week_end);
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
        if(!empty($courseInfos)) {
            foreach ($courseInfos as $courseInfo) {
                $this->showWords($courseInfo);
                $params['courseNames'][$courseInfo['courseName']] = [
                    'id' => $courseInfo['id'],
                    'teacher' => $courseInfo['teacherName'],
                    'student' => $courseInfo['studentName'],
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
                    'content' => $content
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
            $courseName = $sub->getNameById($course['subject']);
        }
        if (!empty($courseName)) {
            $course['courseName'] = $courseName[0];
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
        $params['teacher']['selected'] = '';
        $params['teacher']['options']  = [];
        $teacher = new TeacherService();
        $allTeachers = $teacher->getTeachers();
        foreach($allTeachers as $k => $teacher){
            $params['teacher']['options'][] = ['value' => $teacher['id'], 'text' => $teacher['name']];
        }
        $params['student']['selected'] = '';
        $params['student']['options']  = [];
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

        $params['week']['selected'] = '';

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
