<?php
namespace App\Models;

use App\Models\Service\BaseService;

class SubjectService extends BaseService {
    public function __construct(){
        parent::__construct(new Subject(), 'jr_cms');
    }
}
