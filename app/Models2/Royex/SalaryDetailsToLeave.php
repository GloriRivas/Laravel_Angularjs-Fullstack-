<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class SalaryDetailsToLeave extends Model
{
    protected $table = 'royex_salary_details_to_leave';
    protected $primaryKey = 'salary_details_to_leave_id';

    protected $fillable = [
        'salary_details_to_leave_id', 'salary_details_id','leave_type_id','num_of_day'
    ];

}
