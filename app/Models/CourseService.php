<?php
namespace App\Models;

use DB;

class CourseService {
    public function __construct(){
        $this->course = new Course();
        $this->course->setConnection('jr_cms');
    }

    public function getCourses($id = '') {
        $course = $this->course;
        if(!empty($id)) {
            $course = $course->where('id', $id);
        }
        return $course->all();
    }

    public function getCourseNames() {
        return $this->course->pluck('name');
    }

    public function getInfoById($id) {
        return $this->course->where('id', $id)->first();
    }

    public function getPeriodBySid($cid, $sid) {
        return $this->course->where('subject', $cid)->where('student', $sid)->first();
    }

    public function createOne($params) {
        foreach ($params as $key => $value) {
            $this->course->$key = $value;
        }
        return $this->course->save() ? $this->course->id : false;
    }

    public function updateOne($id, $params) {
        $course = $this->course->find($id);
        foreach ($params as $key => $value) {
            $course->$key = $value;
        }
//        dd($course, $params);
        return $course->save() ? $course->id : false;
    }

    public function deleteOne($id) {
        return $this->course->find($id)->delete();
    }

    public function getCoursesInfos($teacher = '', $student = '', $openTime = '', $endTime = '') {
        $course = $this->course;
        if (!empty($student) && $student != -1) {
            $course = $course->where('student', $student);
        }
        if (!empty($teacher) && $teacher != -1) {
            $course = $course->where('teacher', $teacher);
        }
        if (!empty($openTime)) {
            $course = $course->whereRaw("start >= '$openTime'");
        }
        if (!empty($endTime)) {
            $course = $course->whereRaw("end <= '$endTime'");
        }
        return $course->get();
    }

    public function updateCourseStatus($id, $status) {
        return $this->course->where('id', $id)->update(['status' => $status]);
    }

    public function deleteCourseByTeacher($tId) {
        return $this->deleteCourse($tId, '');
    }

    public function deleteCourseByStudent($sId) {
        return $this->deleteCourse('', $sId);
    }

    protected function deleteCourse($tId, $sId) {
        $course = $this->course;
        if (!empty($tId)) {
            $course = $course->where('teacher', $tId);
        }
        if (!empty($sId)) {
            $course = $course->where('student', $sId);
        }
        return $course->delete();
    }

    public function getSubjectById($id) {
        return $this->course->where('id', $id)->pluck('subject');
    }

    public function getIdByName($name) {
        return $this->course->where('subject', 'like', "%$name%")->pluck('id');
    }
}
