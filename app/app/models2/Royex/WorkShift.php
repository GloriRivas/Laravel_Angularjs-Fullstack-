<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $table = 'royex_work_shift';
    protected $primaryKey = 'work_shift_id';

    protected $fillable = [
        'work_shift_id', 'shift_name','start_time','end_time','late_count_time'
    ];
}
