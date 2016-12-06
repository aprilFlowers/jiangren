<?php

namespace App\Http\Controllers;

use App\Models\CourseService;
use App\Models\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request) {
        $params = [];
        $params['controlUrl'] = '/student/index';
        $this->initSearchBar($request, $params);

        $student = new StudentService();
        $students = $student->getStudents();
        foreach ($students as &$student) {
            $student['grade'] = config("language.grade." . $student['grade']);
        }
        $params['students'] = $students;

        return view('student.index', $params);
    }

    public function edit(Request $request) {
        $id = $request->input('id', '');

        $params = [];
        $params['controlUrl'] = '/student';
        $this->initSearchBar($request, $params);

        if($id){
            $student = new StudentService();
            $studentInfos = $student->getInfoById($id);
            $params['grade']['selected'] = $studentInfos['grade'];
            $keys = ['id', 'name', 'phoneNum'];
            foreach ($keys as $key){
                $params['student'][$key] = $studentInfos[$key];
            }
            $baseInfos = json_decode($studentInfos['baseInfos'], true);
            $params['sex']['selected'] = $baseInfos['sex'];
            $baseKeys = ['age', 'school', 'address', 'phoneNum', 'mark'];
            foreach ($baseKeys as $baseKey) {
                $params['student'][$baseKey]  = $baseInfos[$baseKey];
            }

            if(!empty($studentInfos['family'])) {
                $familyInfos = json_decode($studentInfos['family'], true);
                $params['student']['family'] = $familyInfos;
            }

            if(!empty($studentInfos['courseInfos'])) {
                $courseInfos = json_decode($studentInfos['courseInfos'], true);
                $params['student']['course'] = array_keys($courseInfos);
            }
        }

        return view('student.edit', $params);
    }

    public function update(Request $request) {
        $id = $request->input('id');
        $keys = ['name', 'grade', 'phoneNum'];
        foreach ($keys as $key){
            $data[$key] = $request->input($key, '');
        }

        // student baseInfos
        $baseDatas = [];
        $baseKeys = ['sex', 'age', 'school', 'address', 'phoneNum', 'mark'];
        foreach ($baseKeys as $baseKey) {
            $baseDatas[$baseKey] = $request->input($baseKey, '');
        }
        $data['baseInfos'] = json_encode($baseDatas);

        // family
        $familyInfos = [];
        $familyDatas = [];
        $familyKeys = ['parentName', 'contactNum', 'workAddress'];
        foreach ($familyKeys as $familyKey) {
            $familyInfos[$familyKey] = $request->input($familyKey, []);
        }
        if(!empty($familyInfos['parentName'][0])) {
            for ($i = 0; $i < count($familyInfos['parentName']); $i++) {
                $familyDatas[$i] = [
                    'parentName' => $familyInfos['parentName'][$i],
                    'contactNum' => $familyInfos['contactNum'][$i],
                    'workAddress' => $familyInfos['workAddress'][$i],
                ];
            }
        }
        $data['family'] = json_encode($familyDatas);

        // course
        $courseDatas = [];
        $courses = $request->input('course', []);
        $cour = new CourseService();
        foreach ($courses as $course) {
            $courseInfo = $cour->getInfoById($course);
            $courseDatas[$course] = $courseInfo['period'];
        }
        $data['courseInfos'] = json_encode($courseDatas);

        $student = new StudentService();
        if($id){
            $res = $student->updateOne($id, $data);
        }else{
            $res = $student->createOne($data);
        }
        $dir = "/student/index";

        return redirect($dir);
    }

    public function delete(Request $request) {
        $id=$request->input('id', '');
        $student = new StudentService();
        $res = $student->deleteOne($id);
        $course = new CourseService();
        if($res) {
            $res = $course->deleteCourseByStudent($id);
        }
        $dir = "/student/index";
        return redirect($dir);
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
            $stuInfos = $stu->getStudentsInfos($name, $grade, $phoneNum);
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
                $cour = new CourseService();
                if(!empty($stuInfo['courseInfos'])) {
                    $courseInfos = json_decode($stuInfo['courseInfos'], true);
                    foreach ($courseInfos as $c => $p) {
                        $course['currentPeriod'] = $p;
                        $courseInfo = $cour->getInfoById($c);
                        $course['period'] = $courseInfo['period'];
                        $course['courseName'] = $courseInfo['name'];
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
        $params['sex']['options']  = [];
        $sexCof = config('language.sex');
        foreach($sexCof as $_sk => $sex){
            $params['sex']['options'][] = ['value' => $_sk, 'text' => $sex];
        }
        $params['grade']['selected'] = '';
        $params['grade']['options']  = [];
        $gradeCof = config('language.grade');
        foreach($gradeCof as $_ck => $grade){
            $params['grade']['options'][] = ['value' => $_ck, 'text' => $grade];
        }
        $familyDefault[] = [
            'parentName' => '',
            'contactNum' => '',
            'workAddress' => '',
        ];
        $params['students']['familyDefault'] = json_encode($familyDefault);
        $course = new CourseService();
        $params['courses'] = $course->getCourses();
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
}
