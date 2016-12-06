<?php
namespace App\Models;

class TeacherService {
    public function __construct(){
        $this->teacher = new Teacher();
        $this->teacher->setConnection('jr_cms');
    }

    public function getTeachers() {
        return $this->teacher->all();
    }

    public function getInfoById($id) {
        return $this->teacher->where('id', $id)->first();
    }

    public function createOne($params) {
        foreach ($params as $key => $value) {
            $this->teacher->$key = $value;
        }
        return $this->teacher->save() ? $this->teacher->id : false;
    }

    public function updateOne($id, $params) {
        $teacher = $this->teacher->find($id);
        foreach ($params as $key => $value) {
            $teacher->$key = $value;
        }
        return $teacher->save();
    }

    public function deleteOne($id) {
        return $this->teacher->find($id)->delete();
    }

    public function getTeacherNameById($id) {
        return $this->teacher->where('id', $id)->pluck('name');
    }
}