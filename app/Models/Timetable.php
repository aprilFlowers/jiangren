<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model {
    protected $connected = 'jr_cms';
    protected $table = 'timetable';
    protected $fillable = ['course', 'subject', 'student', 'teacher', 'index', 'start', 'end', 'status'];
    public $timestamps = false;
}
