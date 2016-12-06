<?php

namespace App\Http\Controllers;

use App\Models\CourseService;
use App\Models\StudentService;
use App\Models\TeacherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Zizaco\Entrust\Entrust;

class CourseController extends Controller
{
    public function index(Request $request) {
        $params = [];
        $data = [];
        $params['controlUrl'] = '/course/index';
        $this->initSearchBar($request, $params);

        $cou = new CourseService();
        $courses = $cou->getCourses();

        if(!empty($courses)) {
            foreach ($courses as $k => $course) {
                $this->showWords($course);
                $data[] = $course;

            }
        }
        $params['courses'] = $data;

        return view('course.index', $params);
    }

    public function edit(Request $request) {
        $id = $request->input('id', '');

        $params = [];
        $params['controlUrl'] = '/course';
        $this->initSearchBar($request, $params);

        if($id){
            $course = new CourseService();
            $courseInfos = $course->getInfoById($id);
            $keys = ['teacher', 'student'];
            foreach ($keys as $key){
                $params[$key]['selected'] = $courseInfos[$key];
            }
            $_keys = ['id', 'name', 'period'];
            foreach ($_keys as $_key){
                $params['course'][$_key] = $courseInfos[$_key];
            }
        }

        return view('course.edit', $params);
    }

    public function update(Request $request) {
        $id = $request->input('id');
        $keys = ['name', 'student', 'teacher', 'period'];
        foreach ($keys as $key){
            $data[$key] = $request->input($key, '');
        }
        $couService = new CourseService();
        $stuService = new StudentService();
        $hisCourseData = $couService->getInfoById($id);
        $hisPeriod = $hisCourseData['period'];
        if($id){
            $res = $couService->updateOne($id, $data);
        }else{
            $data['status'] = 0;
            $res = $couService->createOne($data);
        }
        // update student courseInfos
        $_data = [];
        $stuInfos = $stuService->getInfoById($data['student']);
        $courseInfos = json_decode($stuInfos['courseInfos'], true);

        if($courseInfos) {
            foreach ($courseInfos as $course => $period) {
                $_data[$course] = $period;
                if ($course == $res) {
                    $_data[$course] = $data['period'] - ($hisPeriod - $period);
                }
            }
        }
        if (!in_array($res, array_keys($courseInfos))) {
            $_data[$res] = $data['period'];
        }
        $coursesData['courseInfos'] = json_encode($_data);
        $r = $stuService->updateOne($data['student'], $coursesData);
        $dir = "/course/index";

        return redirect($dir);
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

    public function query(Request $request) {
        $params = [];
        $params['controlUrl'] = '/course/query';
        $params['admin'] = '';
        $this->initSearchBar($request, $params);

        $keys = ['student', 'teacher', 'openTime', 'endTime'];
        foreach ($keys as $key){
            if (in_array($key, ['openTime', 'endTime'])) {
                $data[$key] = $request->input($key) ? date('Y-m-d H:i:s', strtotime($request->input($key))) : '';
            }else {
                $data[$key] = $request->input($key, '');
            }
        }
        $courses = new CourseService();
        if($request['_token']) {
            $courseInfos = $courses->getCoursesInfos($data['teacher'], $data['student'], $data['openTime'], $data['endTime']);
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
                echo 3;exit;
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
            echo $res ? 1 : 2;exit;
        }
        echo 2;
    }

    public function timetable(Request $request) {
        $params = [];
        $datas = [];
        $time = date('Y-m-d', time());
        $params['controlUrl'] = '/course/timetable';
        $this->initSearchBar($request, $params);
        $tId = $request->input('teacher');
        $week = $request->input('week');

        $course = new CourseService();
        if($request['_token']) {
            $courseInfos = $course->getCoursesInfos($tId);
            $datas = $this->formatTableData($courseInfos, $params);
            $time = $week ? date('Y-m-d', strtotime($week)) : $time;
        }

        $params['time'] = $time;
        $params['datas'] = json_encode($datas);

        return view('course.timetable', $params);
    }

    public function markTimetable(Request $request) {
        $params = [];
        $params['controlUrl'] = '/course/markTimetable';
        $this->initSearchBar($request, $params);

        $course = new CourseService();
        $courseInfos = $course->getCourses();

        $datas = $this->formatTableData($courseInfos, $params);
        $params['datas'] = json_encode($datas);

        return view('course.markTimetable', $params);
    }

    public function saveTimetableData(Request $request) {
        $msg = [];
        $id = $request->input('id');
        $data['start'] = $request->input('start');
        $data['end'] = date('Y-m-d H:i:s', strtotime($data['start'] . '+120 minute'));

        $course = new CourseService();
        $res = $course->updateOne($id, $data);

        if(!$res){
            $msg['errorMsg'] = '操作失败！';
        }
        return json_encode($msg);
    }

    public function deleteTimetableData(Request $request) {
        $msg = [];
        $id = $request->input('id');
        $data['start'] = '';
        $data['end'] = '';

        $course = new CourseService();
        $res = $course->updateOne($id, $data);

        if(!$res){
            $msg['errorMsg'] = '操作失败！';
        }
        return json_encode($msg);
    }

    protected function formatTableData($courseInfos, &$params) {
        $datas = [];
        $teacher = new TeacherService();
        if(!empty($courseInfos)) {
            foreach ($courseInfos as $courseInfo) {
                $teacherName = $teacher->getTeacherNameById($courseInfo['teacher']);
                $params['courseNames'][$courseInfo['name']] = ['id' => $courseInfo['id'], 'teacher' => $teacherName[0]];
                $color = [
                    'backgroundColor' => "#f39c12",
                    'borderColor' => "#f39c12" // default yellow
                ];
                if ($courseInfo['status'] == 1) {
                    $color = [
                        'backgroundColor' => "#00a65a",
                        'borderColor' => "#00a65a" // green
                    ];
                }
                $data = [
                    'id' => $courseInfo['id'],
                    'title' => $courseInfo['name']."(".$teacherName[0].")",
                    'start' => date('Y-m-d H:i', strtotime($courseInfo['start'])),
                    'end' => date('Y-m-d H:i', strtotime($courseInfo['end'])),
                ];
                $datas[] = array_merge($data, $color);
            }
        }
        return $datas;
    }

    protected function showWords(&$course) {
        $stu = new StudentService();
        $tea = new TeacherService();
        $course['status'] = $course['status'] ? '已确认' : '未确认';
        $studentName = $stu->getStudentNameById($course['student']);
        if (!empty($studentName)) {
            $course['studentName'] = $studentName[0];
        }
        $teacherName = $tea->getTeacherNameById($course['teacher']);
        if (!empty($teacherName)) {
            $course['teacherName'] = $teacherName[0];
        }
    }

    protected function initSearchBar($request, &$params) {
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
            $params['section']['options'][] = ['value' => $k, 'text' => $section];
        }
        $params['week']['selected'] = '';
        $params['week']['options']  = [];
        $allWeeks = config('language.week');
        foreach($allWeeks as $k => $week){
            $params['week']['options'][] = ['value' => $k, 'text' => $week];
        }
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
