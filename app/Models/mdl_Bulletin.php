<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Bulletin extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'ind_notification';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
