<?php

namespace App\Http\Controllers;

use App\Models\CourseService;
use App\Models\StudentService;
use App\Models\SubjectService;
use App\Models\TeacherService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->studentService = new StudentService();
        $this->courseService = new CourseService();
        $this->subjectService = new SubjectService();
        $this->teacherService = new TeacherService();
    }

    public function index(Request $request) {
        $params = [];
        $this->initVueOptions($request, $params);
        $params['students'] = $this->studentService->getInfoByQuery([
            'name' => $request->input('name', ''),
            'grade' => $request->input('grade', ''),
            'phoneNum' => $request->input('phoneNum', ''),
        ]);
        return view('student.index', $params);
    }

    public function edit(Request $request) {
        $params = [];
        $this->initVueOptions($request, $params);
        $this->initSubjectOptions($request, $params);
        $this->initTeacherOptions($request, $params);
//dd($params);
        if($id = $request->input('id')){
            $student = $this->studentService->getInfoById($id);
            $params['student'] = $student;
            if (!empty($student['id'])) {
                $params['student']['courses'] = $this->courseService->getInfoByQuery(['student' => $student['id']]);
            }
        }
        return view('student.edit', $params);
    }

    public function update(Request $request) {
        // student
        $data = [];
        foreach (['name', 'grade', 'phoneNum'] as $key){
            $data[$key] = $request->input($key, '');
        }
        // student baseInfos
        $data['baseInfos'] = [];
        foreach (['sex', 'age', 'school', 'address', 'mark'] as $key) {
            $data['baseInfos'][$key] = $request->input($key, '');
        }
        // student family
        $data['family'] = [];
        foreach (['parentName', 'contactNum', 'workAddress'] as $key) {
            foreach ($request->input($key, []) as $i => $v) {
                $data['family'][$i][$key] = $v;
            }
        }
        // student courses
        $data['courses'] = [];
        foreach ($request->input('cIds', []) as $i => $cId) {
            $data['courses'][] = [
                'id' => $cId,
                'subject' => $request->input("subjects.$i", ''),
                'teacher' => $request->input("teachers.$i", ''),
                'period' => $request->input("periods.$i", ''),
            ];
        }
        // create or update student
        if($id = $request->input('id')){
            $this->studentService->updateOne($id, $data);
        }else{
            $this->studentService->createOne($data);
        }
        return redirect("/student/index");
    }

    public function delete(Request $request) {
        if ($id = $request->input('id', '')) {
            $this->studentService->disableOne($id);
        }
        return redirect("/student/index");
    }

    public function query(Request $request) {
        $params = [];
        $params['controlUrl'] = '/student/query';
        $this->initSearchBar($request, $params);

        $name = $request->input('name');
        $grade = $request->input('grade');
        $phoneNum = $request->input('phoneNum');

        if($request['_token']) {
            $students = [];
            $stu = new StudentService();
            $stuInfos = $this->studentService->getStudentsInfos($name, $grade, $phoneNum);
            foreach ($stuInfos as $stuInfo) {
                // baseInfos
                $keys = ['name', 'grade', 'phoneNum'];
                foreach ($keys as $key){
                    $student[$key] = $stuInfo[$key];
                }
                $student['grade'] = config("language.grade." . $student['grade']);
                $baseInfos = json_decode($stuInfo['baseInfos'], true);
                $baseKeys = ['sex', 'age', 'school', 'address', 'phoneNum', 'mark'];
                foreach ($baseKeys as $baseKey) {
                    $student[$baseKey] = $baseInfos[$baseKey];
                }
                $student['sex'] = config("language.sex." . $student['sex']);
                // family
                if(!empty($stuInfo['family'])) {
                    $familyInfos = json_decode($stuInfo['family'], true);
                    $student['family'] = $familyInfos;
                }

                // course
                $courses = [];
                if(!empty($stuInfo['courseInfos'])) {
                    $courseInfos = json_decode($stuInfo['courseInfos'], true);
                    foreach ($courseInfos as $c => $p) {
                        $course['currentPeriod'] = $p;
                        $courseInfo = $this->courseService->getInfoById($c);
                        $course['period'] = $courseInfo['period'];
                        $course['courseId'] = $courseInfo['name'];
                        $courses[] = $course;
                    }
                }
                $student['course'] = $courses;
                $students[] = $student;
            }
            $params['student'] = $students;
        }

        return view('student.query', $params);
    }

    protected function initSearchBar($request, &$params) {
        $params['sex']['selected'] = '';
        $sexConf = config('language.sex');
        foreach($sexConf as $_sk => $sex){
            $params['sex']['options'][] = ['value' => $_sk, 'text' => $sex];
        }
        $params['grade']['selected'] = '-1';
        $params['grade']['options'][] = ['value' => -1, 'text' => '全部年级'];
        foreach(config("language.grade", []) as $_gk => $grade){
            $params['grade']['options'][] = ['value' => $_gk, 'text' => $grade];
        }

        $familyDefault[] = [
            'parentName' => '',
            'contactNum' => '',
            'workAddress' => '',
        ];
        $params['students']['familyDefault'] = json_encode($familyDefault);
        $this->getCourseInfoOpts($params);
        if($request['_token']){
            $params['_token'] = $request['_token'];
            if(!empty($name = $request->input('name'))){
                $params['name']= $name;
            }
            if(!empty($grade = $request->input('grade'))){
                $params['grade']['selected'] = $grade;
            }
            if(!empty($phoneNum = $request->input('phoneNum'))){
                $params['phoneNum'] = $phoneNum;
            }
        }
    }

    protected function getCourseInfoOpts(&$params, $courseSel = '', $teacherSel = '', $period = '', $id = '', $restPeriod = '') {
        $courseInfos['course']['selected'] = $courseSel;
        $courseInfos['teacher']['selected'] = $teacherSel;
        $courseConf = $this->subjectService->getSubject();
        $teacherConf = $this->teacherService->getTeachers();
        foreach($courseConf as $course){
            $courseInfos['course']['options'][] = ['value' => $course['id'], 'text' => $course['name']];
        }
        foreach($teacherConf as $teacher){
            $courseInfos['teacher']['options'][] = ['value' => $teacher['id'], 'text' => $teacher['name']];
        }
        $courseInfos['period']= $period;
        $courseInfos['restPeriod'] = $restPeriod;
        $courseInfos['id'] = $id;
        $params['courseOpts'] = $courseInfos['course']['options'];
        $params['teacherOpts'] = $courseInfos['teacher']['options'];
        $params['student']['courses'][] = $courseInfos;
    }
}
