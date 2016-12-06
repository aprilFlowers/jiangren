<?php

namespace App\Http\Controllers\Auth;

use App\Models\Auth\PermissionService;
use App\Models\Auth\RoleService;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    //
    public function index(Request $request){
        $params = [];
        $params['controlUrl'] = "/auth/role";
        $role = new RoleService();
        $params['role'] = $role->getInfo();
        return view('auth.role', $params);
    }

    public function edit(Request $request){
        $id = $request->input('id');

        $params = [];
        $params['controlUrl'] = "/auth/role";
        $role = new RoleService();
        $permission = new PermissionService();

        $params['permission'] = $permission->getIdDisNameList();
        if($id){
            $params['role'] = $role->getInfoById($id);
            $params['per'] = $role->getPerByRId($id)->toArray();
        }else{
        }

        return view('auth.roleEdit', $params);
    }

    public function update(Request $request){
        $url = "/auth/role";
        $id = $request->input('id');
        $permissionIds = $request->input('permission_id', []);

        // update role info
        $params = [];
        $keys = ['name', 'display_name', 'description'];
        foreach ($keys as $key){
            $params[$key] = $request->input($key, '');
        }

        $service = new RoleService();
        if($id){
            $res = $service->updateOneWithPers($id, $params, $permissionIds);
        }else{
            $res = $service->createOneWithPers($params, $permissionIds);
        }

        return redirect($url);
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $service = new RoleService();
        $service->deleteOne($id);
        $dir = "/auth/role";
        return redirect($dir);
    }
}
