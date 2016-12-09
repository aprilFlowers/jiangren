<?php
namespace App\Models;

use DB;

class TimetableService {
    public function __construct(){
        $this->timetable = new Timetable();
        $this->timetable->setConnection('jr_cms');
    }

    public function getCourses($id = '') {
        $timetable = $this->timetable;
        if(!empty($id)) {
            $timetable = $timetable->where('id', $id);
        }
        return $timetable->all();
    }

    public function getInfoById($id) {
        return $this->timetable->where('id', $id)->first();
    }

    public function createOne($params) {
        foreach ($params as $key => $value) {
            $this->timetable->$key = $value;
        }
        return $this->timetable->save() ? $this->timetable->id : false;
    }

    public function updateOne($id, $params) {
        $timetable = $this->timetable->find($id);
        foreach ($params as $key => $value) {
            $timetable->$key = $value;
        }
        return $timetable->save() ? $timetable->id : false;
    }

    public function deleteOne($id) {
        return $this->timetable->where('id', $id)->delete();
    }

    public function getTableInfos($teacher = '', $openTime = '', $endTime = '', $student = '') {
        $timetable = $this->timetable;
        if (!empty($teacher)) {
            $timetable = $timetable->where('teacher', 'like', "%$teacher%");
        }
        if (!empty($openTime)) {
            $timetable = $timetable->whereRaw("start >= '$openTime'");
        }
        if (!empty($endTime)) {
            $timetable = $timetable->whereRaw("end <= '$endTime'");
        }
        if (!empty($student)) {
            $timetable = $timetable->where('student', $student);
        }
        return $timetable->get();
    }

    public function updateCourseStatus($id) {
        return $this->timetable->where('id', $id)->update(['status' => 1]);
    }

    public function deleteCourseByTeacher($tId) {
        return $this->deleteCourse($tId, '');
    }

    public function deleteCourseByStudent($sId) {
        return $this->deleteCourse('', $sId);
    }

}