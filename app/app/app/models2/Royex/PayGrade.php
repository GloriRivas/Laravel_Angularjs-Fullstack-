<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class PayGrade extends Model
{
    protected $table = 'royex_pay_grade';
    protected $primaryKey = 'pay_grade_id';

    protected $fillable = [
        'pay_grade_id','pay_grade_name','gross_salary','percentage_of_basic','basic_salary','overtime_rate'
    ];
}
