<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $table = 'royex_interview';
    protected $primaryKey = 'interview_id';

    protected $fillable = [
        'interview_id', 'job_applicant_id','interview_date','interview_time','interview_type','comment'
    ];
}
