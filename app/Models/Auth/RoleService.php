<?php
namespace App\Models\Auth;

use App\Models\Service\BaseService;

class RoleService extends BaseService {
    public function __construct(){
        parent::__construct(new Role(), 'jr_cms');
    }

    public function getInfo(){
        $res = $this->model
            ->select('roles.id as id', 'roles.display_name as role', \DB::raw('GROUP_CONCAT(permissions.display_name) as permission'))
            ->leftJoin('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->leftJoin('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->groupBy('roles.id')
            ->orderBy('roles.id', 'desc')->get();
        return $res;
    }

    public function getPerByRId($roleId){
        $res = $this->model
            ->leftJoin('permission_role', 'permission_role.role_id', '=', \DB::raw($roleId))
            ->leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
            ->pluck('permissions.display_name as permission', 'permissions.id as id');
        return $res;
    }

    public function getIdDisNameList(){
        return $this->model->pluck('display_name', 'id');
    }

    public function getRoleById($id){
        return $this->model->where('id', $id)->pluck('display_name', 'id');
    }

    public function createOneWithPers($params, $permissions){
        foreach ($params as $key=>$value){
            $this->model->$key = $value;
        }
        $res = $this->model->save()? $this->model->id : false;
        $this->model->perms()->sync($permissions);
        return $res;
    }

    public function updateOneWithPers($id, $params, $permissions){
        $obj = $this->model->find($id);
        foreach ($params as $key=>$value){
            $obj->$key = $value;
        }
        $obj->perms()->sync($permissions);
        return $obj->save();
    }
}
