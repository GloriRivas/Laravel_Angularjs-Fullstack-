<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class HolidayDetails extends Model
{
    protected $table = 'royex_holiday_details';
    protected $primaryKey = 'holiday_details_id';

    protected $fillable = [
        'holiday_details_id','holiday_id', 'from_date','to_date','comment'
    ];

    public function holiday(){
        return $this->belongsTo(Holiday::class,'holiday_id','holiday_id');
    }
}
