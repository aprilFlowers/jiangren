<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model {
    protected $connected = 'jr_cms';
    protected $table = 'teachers';
    protected $fillable = ['userId', 'name', 'sex', 'phoneNum', 'type', 'remark', 'status'];
}
