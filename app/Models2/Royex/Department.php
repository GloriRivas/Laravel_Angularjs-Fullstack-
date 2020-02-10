<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'royex_department';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_id', 'department_name'
    ];
}
