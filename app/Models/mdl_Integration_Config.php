<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Integration_Config extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Integration_Config';
    protected $primaryKey = 'Config_Id';
    public $timestamps    = false;
}
