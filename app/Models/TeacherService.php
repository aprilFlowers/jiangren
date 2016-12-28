<?php
namespace App\Models;

use App\Models\Service\BaseService;

class TeacherService extends BaseService {
    public function __construct(){
        parent::__construct(new Teacher(), 'jr_cms');
    }
}
