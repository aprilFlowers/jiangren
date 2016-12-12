<?php
namespace App\Models;

class SubjectService {
    public function __construct(){
        $this->subject = new Subject();
        $this->subject->setConnection('jr_cms');
    }

    public function getSubject() {
        return $this->subject->all();
    }

    public function getInfoById($id) {
        return $this->subject->where('id', $id)->first();
    }

    public function createOne($params) {
        foreach ($params as $key => $value) {
            $this->subject->$key = $value;
        }
        return $this->subject->save() ? $this->subject->id : false;
    }

    public function updateOne($id, $params) {
        $subject = $this->subject->find($id);
        foreach ($params as $key => $value) {
            $subject->$key = $value;
        }
        return $subject->save();
    }

    public function deleteOne($id) {
        return $this->subject->find($id)->delete();
    }

    public function getNameById($id) {
        return $this->subject->where('id', $id)->pluck('name');
    }

    public function getIdByName($name) {
        return $this->subject->where('name', 'like', "%$name%")->pluck('id');
    }
}