<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_GenBulletin extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'notification';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
