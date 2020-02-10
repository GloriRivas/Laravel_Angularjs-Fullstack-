<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'royex_designation';
    protected $primaryKey = 'designation_id';

    protected $fillable = [
        'designation_id', 'designation_name'
    ];


}
