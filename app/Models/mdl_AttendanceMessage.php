<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_AttendanceMessage extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Messages';
    protected $primaryKey = 'Messages_Id';
    public $timestamps    = false;
}
