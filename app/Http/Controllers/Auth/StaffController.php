<?php

namespace App\Http\Controllers\Auth;

use App\Models\Auth\RoleService;
use App\Models\Auth\StaffService;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    //
    public function index(Request $request){
        $params = [];
        $params['controlUrl'] = "/auth/user";
        $staff = new StaffService();
        $params['staff'] = $staff->getInfo();
        return view('auth.staff', $params);
    }

    public function edit(Request $request){
        $id = $request->input('id');

        $params = [];
        $params['controlUrl'] = "/auth/user";
        $role = new RoleService();
        $staff = new StaffService();

        $params['roles'] = $role->getIdDisNameList();
        if($id){
            $params['staff'] = $staff->getInfoById($id);
            $_roles = [];
            foreach ($staff->getInfoById($id)->roles as $r) {
                $_roles[$r->id] = $r->display_name;
            };
            $params['role'] = $_roles;
        }else{
        }
        return view('auth.staffEdit', $params);
    }

    public function update(Request $request){
        $url = "/auth/user";
        $id = $request->input('id');
        $roleIds = $request->input('role', []);

        // update role info
        $params = [];
        $keys = ['name'];
        foreach ($keys as $key){
            $params[$key] = $request->input($key, '');
        }

        $service = new StaffService();
        if($id){
            $res = $service->updateOneWithRoles($id, $params, $roleIds);
        }else{
            $params['password'] = md5($request->input('password'));
            $res = $service->createOneWithRoles($params, $roleIds);
        }

        return redirect($url);
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $service = new StaffService();
        $service->deleteOne($id);
        $dir = "/auth/user";
        return redirect($dir);
    }

    public function changePWD(Request $request){
        $params = [];
        $params['controlUrl'] = "/auth/user/changePWD";
        $params['id'] = $request->input('id');
        return view('auth.changePWD', $params);
    }

    public function pwdUpdate(Request $request){
        $id = $request->input('id');
        $password = md5($request->input('password'));
        $service = new StaffService();
        $service->changePWD($id, $password);
        $dir = "/auth/user";
        return redirect($dir);
    }
}
