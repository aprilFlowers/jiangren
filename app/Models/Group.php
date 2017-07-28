<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {
    protected $connected = 'jr_cms';
    protected $table = 'stu_group';
    protected $fillable = ['id', 'subject', 'student', 'teacher', 'cType', 'status'];
    public $timestamps = false;

}
