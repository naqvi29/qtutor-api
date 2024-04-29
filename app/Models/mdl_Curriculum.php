<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Curriculum extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Curriculum';
    protected $primaryKey = 'Curriculum_Id';
    public $timestamps    = false;

    public function curriculumInfo()
    {
        return $this->hasMany(mdl_Curriculum_Info::class,'Curriculum_Id','Curriculum_Id');
    }

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('Active', '=', 1);
    }
}
