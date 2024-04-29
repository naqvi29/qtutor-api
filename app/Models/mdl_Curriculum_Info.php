<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Curriculum_Info extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Curriculum_Info';
    protected $primaryKey = 'Curriculum_Info_Id';
    public $timestamps    = false;

}
