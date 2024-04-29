<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_PlansMeta extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'tbl_Course_Plan_Meta';
    protected $primaryKey = 'Meta_Id';
    public $timestamps    = false;


    public function plans()
    {
        return $this->belongsTo(mdl_Plans::class,'Plan_Id','Plan_Id');
    }


    public function course()
    {
        return $this->belongsTo(mdl_Courses::class,'Course_Id','Course_Id');
    }

    public function plansNew()
    {
        return $this->hasManyThrough(mdl_Courses::class,mdl_Plans::class,'Plan_Id','Course_Id','Plan_Id','Plan_Id');
    }

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('Active', '=', 1);
    }
}
