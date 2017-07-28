<?php
namespace App\Models;

use App\Models\Service\BaseService;
use App\Models\CourseService;

class StudentService extends BaseService {
    public function __construct(){
        parent::__construct(new Student(), 'jr_cms');
    }

    public function getInfo(){
        $list = parent::getInfo();
        return $this->formatOutputList($list);
    }

    public function getAvailable(){
        $list = parent::getAvailable();
        return $this->formatOutputList($list);
    }

    public function getInfoById($id){
        $obj = parent::getInfoById($id);
        return $this->formatOutput($obj);
    }

    public function getInfoByQuery($query, $notEmpty = true) {
        $list = parent::getInfoByQuery($query, $notEmpty);
        return $this->formatOutputList($list);
    }

    public function createOne($params){
        $_params = $this->formatInput($params);
        $id = parent::createOne($_params);
//        $res = $this->updateStudentCourses($id, $params);
        return $id;
    }

    public function updateOne($id, $params){
        $_params = $this->formatInput($params);
//        $res = $this->updateStudentCourses($id, $params);
        return parent::updateOne($id, $_params);
    }

    public function updateStudentCourses($id, $params){
        $obj = parent::getInfoById($id);
        $output = $this->formatOutput($obj);
        $courseInfo = $output['courses'];
        $data = $output;
        $courseData = [];
        foreach ($courseInfo as $o) {
            $courses = $o;
            if($o['cType'] == $params['cType']) {
                if(!empty($o['period'])) {
                    $courses['passPeriod'] += $params['period'];
                }
            }
            $courseData[] = $courses;
        }
        $data['courses'] = $courseData;
        $_params = $this->formatInput($data);
        return parent::updateOne($id, $_params);
    }

    public function disableOne($id){
        $courseService = new CourseService();
        $res = parent::disableOne($id);
        foreach($courseService->getInfoByQuery(['student' => $id]) as $course) {
            $res &= $courseService->disableOne($course['id']);
        }
        return $res;
    }

    protected function formatInputList($inputList) {
        foreach ($inputList as $i => $input) {
            $inputList[$i] = $this->formatInput($input);
        }
        return $inputList;
    }

    protected function formatOutputList($outputList) {
        foreach ($outputList as $i => $output) {
            $outputList[$i] = $this->formatOutput($output);
        }
        return $outputList;
    }

    protected function formatInput($input) {
        if (!empty($input['baseInfos']) && is_array($input['baseInfos'])) {
            $input['baseInfos'] = json_encode($input['baseInfos']);
        }
        if (!empty($input['family']) && is_array($input['family'])) {
            $input['family'] = json_encode($input['family']);
        }
        if (!empty($input['courses']) && is_array($input['courses'])) {
            $input['courses'] = json_encode($input['courses']);
        }
        return $input;
    }

    protected function formatOutput($output) {
        if (!empty($output['baseInfos']) && is_string($output['baseInfos'])) {
            $output['baseInfos'] = json_decode($output['baseInfos'], true);
        }
        if (!empty($output['family']) && is_string($output['family'])) {
            $output['family'] = json_decode($output['family'], true);
        }
        if (!empty($output['courses']) && is_string($output['courses'])) {
            $output['courses'] = json_decode($output['courses'], true);
        }
        return $output;
    }
}
