<?php

namespace App\Http\Controllers;

use App\Models\GroupService;
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
        $this->groupService = new GroupService();
    }

    public function subject(Request $request) {
        $params = [];
        $params['subjects'] = $this->subjectService->getInfo();
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
//            'color' => $request->input('color', ''),
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
        $defaultEndTime = date('Y-m-d H:i:s');
        $defaultOpenTime = date('Y-m-d H:i:s', strtotime('-2 week'));
        $params = [
            'openTime' => $request->input("openTime", $defaultOpenTime),
            'endTime' => $request->input("openTime", $defaultEndTime),
        ];
        $params['admin'] = \Entrust::hasRole(['admin']) ? 'admin' : '';
        $params['controlUrl'] = '/course/index';

        $this->initVueOptions($request, $params);
        $this->initCStatus($request, $params);
        $this->initStudentOptions($request, $params, 1, true);
        $this->initTeacherOptions($request, $params, 1, true);
        $this->initStuGroupOptions($request, $params);

        $teacher = $request->input("teacher", '');
        $student = $request->input("student", '');
        if($status = $request->input('status', '')) {
            $s = [$status];
        } else {
            $s = [1,2];
        }
        $query = [
            'openTime' => ['>=', $request->input("openTime", '')],
            'endTime' => ['<=', $request->input("endTime", '')],
            'status' => ['in', $s], // confirmed
        ];

        if(!empty($teacher)) {
            $query['teacher'] = $teacher;
        }
        if(!empty($student)) {
            $query['student'] = $student;
        }

        $courseInfos = $this->courseService->getInfoByQuery($query);

        foreach ($courseInfos as $k => $c) {
            $course = $c;
            $course['cTypeNum'] = $c['cType'];
            $course['sIdStr'] = str_replace(',', '_', $c['student']);
            list($course['teacher'], $course['subject'], $course['student'], $course['cType']) = $this->getNameInfo($c['teacher'], $c['subject'], $c['student'], $c['cType']);
            $params['course'][] = $course;
        }
        return view('course.index', $params);
    }

    public function edit(Request $request) {
        $id = $request->input('id', '');

        $params = [];
        $params['controlUrl'] = '/course/index';
        $this->initVueOptions($request, $params);
        $this->initTeacherOptions($request, $params, null);
        $this->initStuGroupOptions($request, $params);
        $this->initCourseTime($request, $params);

        if($id){
            $courseInfos = $this->courseService->getInfoById($id);
            $keys = ['teacher', 'student', 'subject', 'stuGroup'];
            foreach ($keys as $key){
                $params[$key]['selected'] = $courseInfos[$key];
            }
            $_keys = ['id', 'period'];
            foreach ($_keys as $_key){
                $params['course'][$_key] = $courseInfos[$_key];
            }
            $params['course']['date'] = date('Y-m-d', strtotime($courseInfos['openTime']));
            $params['openTime']['selected'] = $courseInfos['oSel'];
            $params['endTime']['selected'] = $courseInfos['eSel'];
        }
        return view('course.edit', $params);
    }

    public function update(Request $request) {
        $stuGroup = $request->input('stuGroup', '');
        $date = $request->input('date', '');
        $oSel = $request->input('openTime', '');
        $eSel = $request->input('endTime', '');
        $openTime = config("language.time.$oSel");
        $period = number_format(($eSel - $oSel) / 2, 1);

        $stuGroupInfo = $this->groupService->getInfoById($stuGroup);
        $week = date('w', strtotime($date));
        $time = '';
        foreach (config('language.study.lesson') as $k => $lesson) {
            $t = 0;
            if(strtotime($lesson['start']) <= strtotime($openTime)) {
                $t = $k + 1;
                $next = config("language.study.lesson.$t");
                if(strtotime($openTime) < strtotime($next['start'])) {
                    $time = $k;
                }
            }
        }
        $index = $time . (($week + 6) % 7 + 1);
        $params = [
            'teacher' => $request->input('teacher', ''),
            'subject' => $stuGroupInfo['subject'],
            'student' => $stuGroupInfo['student'],
            'cType' => $stuGroupInfo['cType'],
            'oSel' => $oSel,
            'eSel' => $eSel,
            'openTime' => $date . ' ' . config("language.time.$oSel") . ':00',
            'endTime' => $date . ' ' . config("language.time.$eSel") . ':00',
            'period' => $period,
            'stuGroup' => $stuGroup,
            't_index' => $index,
            'status' => 1,
        ];
        if($id = $request->input('id', '')){
            $res = $this->courseService->updateOne($id, $params);
        } else {
            $res = $this->courseService->createOne($params);
        }

        return redirect('/course/index');
    }

    public function delete(Request $request) {
        $id=$request->input('id', '');
        $res = $this->courseService->deleteOne($id);
        $dir = "/course/index";
        return redirect($dir);
    }

    public function clickCourse(Request $request) {
        $cid = $request->input('cid');
        $sIdStr = $request->input('sIdStr');
        $cType = $request->input('cType');
        $period = $request->input('period');

        $msg = [ 'errorCode' => 1, 'errorMsg' => '操作失败！' ];

        // reduce student course passPeriod
        $res = $this->reduceCoursePeriod($sIdStr, $cType, $period);

        //update course startus
        $res = $this->courseService->updateOne($cid, ['status' => 2]);
        if ($res) {
            $msg['errorCode'] = 0;
            $msg['errorMsg'] = '操作成功';
        }
        echo json_encode($msg);
    }

    public function timetable(Request $request) {
        $params = ['table' => []];
        $this->initVueOptions($request, $params);
        $this->initStudentOptions($request, $params);
        $this->initTeacherOptions($request, $params);

        $teacher = $request->input("teacher", $params['vueOptions']['teacher']['selected']);
        $student = $request->input("student", $params['vueOptions']['student']['selected']);

        list($weekStart, $weekEnd) = $this->getWeekStartEnd($request->input('openTime'));
        $query = [
            'openTime' => ['>=', $weekStart],
            'endTime' => ['<=', $weekEnd.' 23:59:59'],
            //'status' => ['in', [1]],
        ];
        if(!empty($teacher)) {
            $query['teacher'] = $teacher;
        }
        if(!empty($student)) {
            $query['student'] = $student;
        }

        $params['time'] = $weekStart.'  --  '.$weekEnd;
        $params['weekStart'] = $weekStart;
        $params['weekEnd'] = $weekEnd;
        $params['lessons'] = config('language.study.lesson', []);
        $params['admin'] = (\Entrust::hasRole(['admin']) || \Entrust::hasRole('teacher')) ? 'admin' : '';
        $courseInfos = $this->courseService->getInfoByQuery($query);

        foreach ($courseInfos as $k => $c) {
            $course = $c;
            list($course['teacher'], $course['subject'], $course['student'], $course['cType']) = $this->getNameInfo($c['teacher'], $c['subject'], $c['student'], $c['cType']);
            $courseList[] = $course;
        }

        //add courseInfos
        $params['lessons'] = config('language.study.lesson');
        foreach ($courseInfos as $c) {
            if(array_key_exists($c['t_index'], $params['table'])) {
                $params['table'][$c['t_index']]['total']  += 1;
            } else {
                $date = date('Y-m-d', strtotime($c['openTime']));
                $params['table'][$c['t_index']]['total'] = 1;
                $params['table'][$c['t_index']]['date'] = $date;
            }
            $openTime = config("language.time.{$c['oSel']}");
            $endTime = config("language.time.{$c['eSel']}");
            $params['table'][$c['t_index']]['info'][] = [
                'course' => "{$c['teacher']} | {$c['subject']} | {$c['student']} | $openTime ~ $endTime",
            ];
        }
        foreach ($params['table'] as $k => $v) {
            $params['table'][$k]['info'] = json_encode($v['info']);
        }
        return view('course.timetable', $params);
    }

    public function getStuGroup(Request $request) {
        $params = [];
        $cId = $request->input('cId');
        $this->initStuGroupOptions($request, $params, $cId);
        $res = $params['vueOptions']['stuGroup']['options'];
        return json_encode($res);
    }

    protected function getWeekStartEnd($time = null) {
        $date = $time ? date('Y-m-d', strtotime($time)) : date('Y-m-d');
        $w = date('w', strtotime($date));
        $weekStart = date('Y-m-d', strtotime("$date -".($w ? $w-1: 6).' days'));
        $weekEnd = date('Y-m-d', strtotime("$weekStart +6 days"));
        return [$weekStart, $weekEnd];
    }

    protected function reduceCoursePeriod($sIdStr, $cType, $period) {
        $params = ['cType' => $cType, 'period' => $period];
        $sIds = explode('_', $sIdStr);
        foreach ($sIds as $sid) {
            $res = $this->studentService->updateStudentCourses($sid, $params);
        }
        return $res;
    }
}
