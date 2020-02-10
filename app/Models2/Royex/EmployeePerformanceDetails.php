<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class EmployeePerformanceDetails extends Model
{
    protected $table = 'royex_employee_performance_details';
    protected $primaryKey = 'employee_performance_details_id';

    protected $fillable = [
        'employee_performance_details_id','employee_performance_id', 'performance_criteria_id','rating'
    ];
}
