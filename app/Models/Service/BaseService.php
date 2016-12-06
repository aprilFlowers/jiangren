<?php
namespace App\Models\Service;

class BaseService {
    protected $model;

    public function __construct($model, $connection){
        $this->model = $model;
        $this->model->setConnection($connection);
    }

    public function getInfo(){
        return $this->model->orderBy('id', 'desc')->get();
    }

    public function getInfoById($id){
        return $this->model->find($id);
    }

    public function createOne($params){
        foreach ($params as $key=>$value){
            $this->model->$key = $value;
        }

        return $this->model->save()? $this->model->id : false;
    }

    public function updateOne($id, $params){
        $obj = $this->model->find($id);
        foreach ($params as $key=>$value){
            $obj->$key = $value;
        }
        return $obj->save();
    }

    public function deleteOne($id){
        return $this->model->where('id', $id)->delete();
    }
}
