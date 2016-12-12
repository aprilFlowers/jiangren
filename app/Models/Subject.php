<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model {
    protected $connected = 'jr_cms';
    protected $table = 'subject';
    protected $fillable = ['name'];
    public $timestamps = false;

}