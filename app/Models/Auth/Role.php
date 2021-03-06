<?php

namespace App\Models\Auth;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {
    protected $fillable = ['name', 'display_name', 'description'];
    protected $table = 'roles';
}
