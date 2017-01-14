<?php
namespace App\Models;

use App\Models\Service\BaseService;

class TimetableService extends BaseService {
    public function __construct(){
        parent::__construct(new Timetable(), 'jr_cms');
        $this->timetable = new Timetable();
    }
}
