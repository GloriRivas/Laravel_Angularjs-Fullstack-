<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;


class Role extends Model
{
    protected $table = 'royex_role';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_id', 'role_name'
    ];


}
