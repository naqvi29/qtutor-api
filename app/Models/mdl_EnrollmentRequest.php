<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_EnrollmentRequest extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Enrollment_Requests';
    protected $primaryKey = 'Request_Id';
    public $timestamps    = false;
}
