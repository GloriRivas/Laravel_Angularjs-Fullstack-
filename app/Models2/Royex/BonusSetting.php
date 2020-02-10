<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class BonusSetting extends Model
{
    protected $table = 'royex_bonus_setting';
    protected $primaryKey = 'bonus_setting_id';

    protected $fillable = [
        'bonus_setting_id', 'festival_name','percentage_of_bonus','bonus_type'
    ];
}
