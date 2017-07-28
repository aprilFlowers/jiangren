<?php
namespace App\Models;

use App\Models\Service\BaseService;

class GroupService extends BaseService {
    public function __construct(){
        parent::__construct(new Group(), 'jr_cms');
        $this->group = new Group();
    }

    public function getListByTId($teacher) {
        return $this->group->where('teacher', $teacher)->get();
    }
}
