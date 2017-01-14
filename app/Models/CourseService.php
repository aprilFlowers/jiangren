<?php
namespace App\Models;

use App\Models\Service\BaseService;
use App\Models\SubjectService;
use App\Models\TeacherService;
use App\Models\StudentService;
use App\Models\TimetableService;

class CourseService extends BaseService {
    public function __construct(){
        parent::__construct(new Course(), 'jr_cms');
        $this->course = new Course();
    }

    public function getInfo(){
        $list = parent::getInfo();
        $this->appendDetailInfos($list);
        return $this->formatOutputList($list);
    }

    public function getAvailable(){
        $list = parent::getAvailable();
        $this->appendDetailInfos($list);
        return $this->formatOutputList($list);
    }

    public function getInfoById($id){
        $obj = parent::getInfoById($id);
        $this->appendDetailInfo($obj);
        return $obj;
    }

    public function getInfoByQuery($query, $notEmpty = true) {
        $list = parent::getInfoByQuery($query, $notEmpty);
        $this->appendDetailInfos($list);
        return $this->formatOutputList($list);
    }


    public function getCourseConfirmed($courseId) {
        $timetableService = new TimetableService();
        $confirmed = $timetableService->getInfoByQuery(['course' => $courseId, 'status' => 2]);
        return count($confirmed);
    }

    protected function appendDetailInfos(&$list) {
        $subjects = $this->getSubjects();
        $teachers = $this->getTeachers();
        $students = $this->getStudents();
        foreach ($list as &$obj) {
            $this->appendDetailInfo($obj, $subjects, $teachers, $students);
        }
    }

    protected function appendDetailInfo(&$obj, $subjects = null, $teachers = null, $students = null) {
        // reuse subjects
        if (is_null($subjects)) {
            $subjects = $this->getSubjects();
        }
        if (!empty($subjects[$obj['subject']])) {
            $obj['subjectInfo'] = $subjects[$obj['subject']]->toArray();
        }
        // reuse teachers
        if (is_null($teachers)) {
            $teachers = $this->getTeachers();
        }
        if (!empty($teachers[$obj['teacher']])) {
            $obj['teacherInfo'] = $teachers[$obj['teacher']]->toArray();
        }
        // reuse students
        if (is_null($students)) {
            $students = $this->getStudents();
        }
        if (!empty($students[$obj['student']])) {
            $obj['studentInfo'] = $students[$obj['student']]->toArray();
        }
        // course left
        $confirmed = $this->getCourseConfirmed($obj['id']);
        $obj['periodLeft'] = $obj['period'] - $confirmed * 2;
    }

    protected function getSubjects() {
        $subjectService = new SubjectService();
        $subjects = [];
        foreach ($subjectService->getInfo() as $subject) {
            $subjects[$subject['id']] = $subject;
        }
        return $subjects;
    }

    protected function getTeachers() {
        $teacherService = new TeacherService();
        $teachers = [];
        foreach ($teacherService->getInfo() as $teacher) {
            $teachers[$teacher['id']] = $teacher;
        }
        return $teachers;
    }

    protected function getStudents() {
        $studentService = new StudentService();
        $students = [];
        foreach ($studentService->getInfo() as $student) {
            $students[$student['id']] = $student;
        }
        return $students;
    }

    protected function formatOutputList(&$list) {
        $output = [];
        foreach ($list as $obj) {
            if (!empty($obj['subjectInfo']) && !empty($obj['teacherInfo']) && !empty($obj['studentInfo'])) {
                $output[$obj['id']] = $obj->toArray();
            }
        }
        $list = $output;
        return $output;
    }
}
