<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    protected $connected = 'jr_cms';
    protected $table = 'students';
    protected $fillable = ['userId', 'name', 'grade', 'phoneNum', 'baseInfos', 'family', 'status'];
}
