<?php

namespace App\Http\Controllers\Auth;

use App\Models\Auth\PermissionService;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function index(Request $request){
        $params[] = [];
        $params['controlUrl'] = "/auth/permission";
        $permission = new PermissionService();
        $params['pms'] = $permission->getInfo();
        return view('auth.permission', $params);
    }

    public function edit(Request $request){
        $id = $request->input('id');

        $params[] = [];
        $params['controlUrl'] = "/auth/permission";
        $permission = new PermissionService();
        $params['pms'] = $permission->getInfoById($id);

        return view('auth.permissionEdit', $params);
    }

    public function update(Request $request){
        $url = "/auth/permission";
        $id = $request->input('id');
        $params = [];
        $keys = ['name', 'display_name', 'description'];
        foreach ($keys as $key){
            $params[$key] = $request->input($key, '');
        }

        $service = new PermissionService();
        if($id){
            $res = $service->updateOne($id, $params);
        }else{
            $res = $service->createOne($params);
        }

        return redirect($url);
    }

    public function delete(Request $request){
        $url = "/auth/permission";
        $id=$request->input('id', '');
        $service = new PermissionService();
        $res = $service->deleteOne($id);
        return redirect($url);
    }
}
