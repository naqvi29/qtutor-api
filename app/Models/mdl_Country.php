<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mdl_Country extends Model
{
    use HasFactory;
    protected $guarded    = [];
    protected $table      = 'tbl_Countries';
    protected $primaryKey = 'Country_Id';
    public $timestamps    = false;

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('Active', '=', 1);
    }
}
