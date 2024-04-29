<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Parent extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'guardianLogin';
    protected $primaryKey = 'id';
    public $timestamps    = false;


    public function plansMeta()
    {
        return $this->hasMany(mdl_Users::class,'StudentId','StudentId');
    }
}
