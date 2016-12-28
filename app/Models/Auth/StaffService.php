<?php
namespace App\Models\Auth;

use App\Models\Service\BaseService;

class StaffService extends BaseService {
    public function __construct(){
        parent::__construct(new Staff(), 'jr_cms');
    }

    public function getWithPWD($account, $password) {
        return $this->model->where('name', $account)->where('password', $password)->first();
    }

    public function getName(){
        return $this->model->pluck('name');
    }

    public function getByEmail($email){
        return $this->model->where('email', $email)->first();
    }

    public function createOneWithRoles($params, $roles){
        foreach ($params as $key=>$value){
            $this->model->$key = $value;
        }
        $res = $this->model->save()? $this->model->id : false;
        $this->model->roles()->sync($roles);
        return $res;
    }

    public function updateOneWithRoles($id, $params, $roles){
        $obj = $this->model->find($id);
        foreach ($params as $key=>$value){
            $obj->$key = $value;
        }
        $obj->roles()->sync($roles);
        return $obj->save();
    }

    public function changePWD($id, $password){
        return $this->model->where('id', $id)->update(['password' => $password]);
    }
}
