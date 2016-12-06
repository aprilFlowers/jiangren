<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model {
    protected $connected = 'jr_cms';
    protected $table = 'courses';
    protected $fillable = ['name', 'student', 'teacher', 'period', 'currentPeriod', 'openTime', 'endTime', 'status'];
    public $timestamps = false;
}