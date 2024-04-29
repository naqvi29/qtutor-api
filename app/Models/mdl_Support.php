<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Support extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Support';
    protected $primaryKey = 'Support_Id';
    public $timestamps    = false;
}
