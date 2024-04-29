<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_EnrolledCourses extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'course';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
