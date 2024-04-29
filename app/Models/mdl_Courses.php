<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Courses extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'tbl_Courses';
    protected $primaryKey = 'Course_Id';
    public $timestamps    = false;

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('Active', '=', 1);
    }
}
