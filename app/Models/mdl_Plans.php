<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Plans extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'tbl_Course_Plan';
    protected $primaryKey = 'Plan_Id';
    public $timestamps    = false;

    public function plansMeta()
    {
        return $this->hasMany(mdl_PlansMeta::class,'Plan_Id','Plan_Id');
    }

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('Active', '=', 1);
    }
}
