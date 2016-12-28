<?php
namespace App\Models\Service;

class BaseService {
    protected $model;

    public function __construct($model, $connection){
        $this->model = $model;
        $this->model->setConnection($connection);
    }

    public function getInfo(){
        return $this->model->get();
    }

    public function getAvailable(){
        return $this->model->where('status', 1)->get();
    }

    public function getInfoById($id){
        return $this->model->find($id);
    }

    public function getInfoByQuery($query, $notEmpty = true) {
        $model = clone $this->model;
        foreach ($query as $key => $value) {
            if (is_array($value) && count($value) == 2) {
                if (empty($value[1]) && $notEmpty) continue;
                if ($value[0] == 'in') {
                    $model = $model->whereIn($key, $value[1]);
                } elseif($value[0] == 'not in') {
                    $model = $model->whereNotIn($key, $value[1]);
                } else {
                    $model = $model->where($key, $value[0], $value[1]);
                }
            } elseif(!is_array($value)) {
                if (empty($value) && $notEmpty) continue;
                $model = $model->where($key, $value);
            }
        }
        return $model->get();
    }

    public function createOne($params){
        $model = clone $this->model;
        foreach ($model->getFillable() as $attribute) {
            if (isset($params[$attribute])) $model->$attribute = $params[$attribute];
        }
        return $model->save()? $model->id : false;
    }

    public function updateOne($id, $params){
        $data = [];
        foreach ($this->model->getFillable() as $attribute) {
            if (isset($params[$attribute])) $data[$attribute] = $params[$attribute];
        }
        return $this->model->where('id', $id)->update($data);
    }

    public function deleteOne($id){
        return $this->model->where('id', $id)->delete();
    }

    public function disableOne($id){
        return $this->model->where('id', $id)->update(['status' => 0]);
    }
}
