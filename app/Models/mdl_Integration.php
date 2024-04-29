<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Integration extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Integrations';
    protected $primaryKey = 'Integration_Id';
    public $timestamps    = false;
}
