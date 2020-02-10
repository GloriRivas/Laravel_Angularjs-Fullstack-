<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'royex_branch';
    protected $primaryKey = 'branch_id';

    protected $fillable = [
        'branch_id', 'branch_name'
    ];
}
