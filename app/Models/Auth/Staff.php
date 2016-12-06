<?php

namespace App\Models\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class Staff extends Model implements AuthenticatableContract//, AuthorizableContract
{
    use Authenticatable;// Authorizable;
    use EntrustUserTrait;

    protected $connection = 'jr_cms';
    protected $table = 'staff';
    protected $fillable = ['name', 'email', 'password'];
    public $timestamps = false;
}
