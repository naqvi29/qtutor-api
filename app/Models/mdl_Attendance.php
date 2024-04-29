<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Attendance extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'class_attendance';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
