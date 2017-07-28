<?php

namespace App\Http\Controllers;

use App\Models\CourseService;
use App\Models\GroupService;
use App\Models\StudentService;
use App\Models\SubjectService;
use App\Models\Auth\RoleService;
use App\Models\Auth\StaffService;
use App\Models\TeacherService;
use Illuminate\Http\Request;

use Auth;
use Entrust;

class StudentController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->studentService = new StudentService();
        $this->courseService = new CourseService();
        $this->subjectService = new SubjectService();
        $this->roleService = new RoleService();
        $this->staffService = new StaffService();
        $this->groupService = new GroupService();
        $this->teacherService = new TeacherService();
    }

    public function index(Request $request) {
        $params = [
            'name' => $request->input('name', ''),
            'phoneNum' => $request->input('phoneNum', ''),
            'gradeLang' => config('language.study.grade'),
        ];
        $this->initVueOptions($request, $params);
        if (Entrust::hasRole('student')) {
            $params['students'] = $this->studentService->getInfoByQuery([
                'userId' => Auth::user()->id,
            ]);
        } else {
            $params['students'] = $this->studentService->getInfoByQuery([
                'name' => $request->input('name', ''),
                'grade' => $request->input('grade', ''),
                'phoneNum' => $request->input('phoneNum', ''),
            ]);
        }
        return view('student.index', $params);
    }

    public function edit(Request $request) {
        $params = [];
        $params['student'] = [];
        $params['student']['courses'] = [];
        $this->initVueOptions($request, $params);
        $this->initCourseType($request, $params);
//        $this->initTeacherOptions($request, $params, 1, true);

        if($id = $request->input('id')){
            $student = $this->studentService->getInfoById($id)->toArray();
            $student['staff'] = $this->staffService->getInfoById($student['userId']);
            $params['student'] = $student;
            $params['student']['courses'] = [];
            if (!empty($student['courses'])) {
                $keys = ['id', 'cType', 'period', 'passPeriod'];
                foreach ($student['courses'] as $course) {
                    foreach ($keys as $key) {
                        $courseInfo[$key] = $course[$key];
                    }
                    $courseInfo['restPeriod'] = $courseInfo['period'] - $courseInfo['passPeriod'];
                    $params['student']['courses'][] = $courseInfo;
                }
            }
        }

        return view('student.edit', $params);
    }

    public function update(Request $request) {
        $userId = $request->input('userId', '');
        $password = $request->input('password', '');
        // create auth user
        if (empty($userId)) {
            $params = [
                'name' => $request->input('name', ''),
                'password' => md5($password),
            ];
            $userId = $this->staffService->createOneWithRoles($params, [2]);
        } elseif(!empty($password) && $password != "password_setted") {
            // update auth user password
            $this->staffService->changePWD($userId, md5($password));
        }
        // student
        $data = [
            'userId' => $userId,
        ];
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
                'cType' => $request->input("cType.$i", ''),
//                'teacher' => $request->input("teachers.$i", ''),
                'period' => $request->input("periods.$i", ''),
                'passPeriod' => $request->input("passPeriod.$i", 0),
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
            // delete auth user
            $student = $this->studentService->getInfoById($id);
            $staff = $this->staffService->getInfoById($student['userId']);
            $this->staffService->deleteOne($staff['id']);
            // disable
            $this->studentService->disableOne($id);
        }
        return redirect("/student/index");
    }

    public function deleteCourse(Request $request) {
        $cId = $request->input('cId');
        $res = $this->courseService->deleteOne($cId);
        $msg['errorMsg'] = $res ? '操作成功！' : '操作失败！';
        echo json_encode($msg);
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

    public function group(Request $request) {
        $params = [];
        $params['controlUrl'] = '/student/group';
        $groupList = $this->groupService->getInfo();
        foreach ($groupList as $group) {
            list($g['teacher'], $g['subject'], $g['student'], $g['cType']) = $this->getNameInfo($group['teacher'], $group['subject'], $group['student'], $group['cType']);
            $g['id'] = $group['id'];
            $params['group'][] = $g;
        }

        return view('student.group', $params);
    }

    public function groupEdit(Request $request) {
        $params = [];
        $params['controlUrl'] = '/student/group';
        $this->initSubjectOptions($request, $params);
        $this->initTeacherOptions($request, $params, 1, true);
        $this->initStudentOptions($request, $params, 1, true);
        $this->initCourseType($request, $params);

        if($id = $request->input('id')) {
            $params['group'] = $this->groupService->getInfoById($id);
            $params['group']['student'] = explode(',', $params['group']['student']);
        }

        return view('student.groupEdit', $params);
    }

    public function groupUpdate(Request $request) {
        $params = [];
        $params['controlUrl'] = '/student/group';

        $keys = ['teacher', 'subject', 'cType'];
        foreach ($keys as $key) {
            $data[$key] = intval($request->input($key));
        }
        $data['student'] = join(',', $request->input('student'));
        $data['status'] = 1;

        if($id = $request->input('id')) {
            $res = $this->groupService->updateOne($id, $data);
        } else {
            $res = $this->groupService->createOne($data);

        }
        return redirect("/student/group");
    }

    public function groupDelete(Request $request) {
        if ($id = $request->input('id', '')) {
            $this->groupService->deleteOne($id);
        }
        return redirect("/student/group");
    }
}
