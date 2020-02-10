<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'royex_holiday';
    protected $primaryKey = 'holiday_id';

    protected $fillable = [
        'holiday_id', 'holiday_name'
    ];
}
