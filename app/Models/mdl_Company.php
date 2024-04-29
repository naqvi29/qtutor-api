<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Company extends Model
{
    use HasFactory;

    protected $guarded    = [];
    protected $table      = 'tbl_Company';
    protected $primaryKey = 'Company_ID';
    public $timestamps    = false;
}
