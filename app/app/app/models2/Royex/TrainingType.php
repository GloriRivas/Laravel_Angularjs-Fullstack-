<?php

namespace App\Models2\Royex;

use Illuminate\Database\Eloquent\Model;

class TrainingType extends Model
{
    protected $table = 'royex_training_type';
    protected $primaryKey = 'training_type_id';

    protected $fillable = [
        'training_type_id', 'training_type_name','status'
    ];
}
