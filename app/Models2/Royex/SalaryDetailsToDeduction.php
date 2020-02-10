<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class SalaryDetailsToDeduction extends Model
{
    protected $table = 'royex_salary_details_to_deduction';
    protected $primaryKey = 'salary_details_to_deduction_id';

    protected $fillable = [
        'salary_details_to_deduction_id', 'salary_details_id','deduction_id','amount_of_deduction'
    ];


}
