<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $table = 'royex_deduction';
    protected $primaryKey = 'deduction_id';

    protected $fillable = [
        'deduction_id', 'deduction_name','deduction_type','percentage_of_basic','limit_per_month'
    ];
}
