<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model {
    protected $connected = 'jr_cms';
    protected $table = 'courses';
    protected $fillable = ['subject', 'student', 'teacher', 'period'];
    public $timestamps = false;
}