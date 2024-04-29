<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_CourseCategories extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'tbl_Course_Categories';
    protected $primaryKey = 'Category_Id';
    public $timestamps    = false;
}

