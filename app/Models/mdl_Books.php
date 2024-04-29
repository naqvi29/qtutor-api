<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Books extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'book_download';
    protected $primaryKey = 'id';
    public $timestamps    = false;
}
