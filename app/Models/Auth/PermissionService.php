<?php
namespace App\Models\Auth;

use App\Models\Service\BaseService;

class PermissionService extends BaseService {
    public function __construct(){
        parent::__construct(new Permission(), 'jr_cms');
    }

    public function getIdDisNameList(){
        return $this->model->pluck('display_name', 'id');
    }
}
