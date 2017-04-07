<?php

namespace App\Http\Controllers;

use App\Models\Auth\RoleService;
use App\Models\Auth\StaffService;
use App\Models\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->teacherService = new TeacherService();
        $this->roleService = new RoleService();
        $this->staffService = new StaffService();
    }

    public function index(Request $request) {
        $params = [];
        $params['teachers'] = $this->teacherService->getInfo();
        return view('teacher.index', $params);
    }

    public function edit(Request $request) {
        $params = [];
        $this->initVueOptions($request, $params);
        // get data
        if($id = $request->input('id')){
            $teacher = $this->teacherService->getInfoById($id);
            $teacher['staff'] = $this->staffService->getInfoById($teacher['userId']);
            $params['teacher'] = $teacher;
        }
        return view('teacher.edit', $params);
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
            $userId = $this->staffService->createOneWithRoles($params, [1]);
        } elseif(!empty($password) && $password != "password_setted") {
            // update auth user password
            $this->staffService->changePWD($userId, md5($password));
        }
        // prepare update data
        $data = [
            'userId' => $userId,
        ];
        foreach (['name', 'sex', 'phoneNum'] as $key){
            $data[$key] = $request->input($key);
        }
        // update or create
        if($id = $request->input('id')){
            $this->teacherService->updateOne($id, $data);
        }else{
            $this->teacherService->createOne($data);
        }
        return redirect("/teacher/index");
    }

    public function delete(Request $request) {
        if($id = $request->input('id')) {
            // delete auth user
            $teacher = $this->teacherService->getInfoById($id);
            $staff = $this->staffService->getInfoById($teacher['userId']);
            $this->staffService->deleteOne($staff['id']);
            // disable
            $this->teacherService->disableOne($id);
        }
        return redirect("/teacher/index");
    }

}
