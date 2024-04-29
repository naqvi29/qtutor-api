<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_ClassRecording extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'class_recordings';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
