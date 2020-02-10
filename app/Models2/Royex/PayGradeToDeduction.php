<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class PayGradeToDeduction extends Model
{
    protected $table = 'royex_pay_grade_to_deduction';
    protected $primaryKey = 'pay_grade_to_deduction_id';

    protected $fillable = [
        'pay_grade_to_deduction_id', 'pay_grade_id','deduction_id'
    ];
}
