<?php
namespace App\Models;

class StudentService {
    public function __construct(){
        $this->student = new Student();
        $this->student->setConnection('jr_cms');
    }

    public function getStudents() {
        return $this->student->all();
    }

    public function getInfoById($id) {
        return $this->student->where('id', $id)->first();
    }

    public function createOne($params) {
        foreach ($params as $key => $value) {
            $this->student->$key = $value;
        }
        return $this->student->save() ? $this->student->id : false;
    }

    public function updateOne($id, $params) {
        $student = $this->student->find($id);
        foreach ($params as $key => $value) {
            $student->$key = $value;
        }
        return $student->save();
    }

    public function deleteOne($id) {
        return $this->student->find($id)->delete();
    }

    public function getStudentsInfos($name = '', $grade = '', $phoneNum = '') {
        $student = $this->student;
        if (!empty($name)) {
            $student = $student->where('name', $name);
        }
        if ($grade >= 0) {
            $student = $student->where('grade', $grade);
        }
        if (!empty($phoneNum)) {
            $student = $student->where('phoneNum', $phoneNum);
        }
        return $student->get();
    }

    public function getStudentNameById($id) {
        return $this->student->where('id', $id)->pluck('name');
    }

    public function updateCoursePeriod($id, $data) {
        return $this->student->where('id', $id)->update(['courseInfos' => $data]);
    }
}
