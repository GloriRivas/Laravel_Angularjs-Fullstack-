<?php

namespace App\Models2;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model {
	public $timestamps = false;
	protected $table = 'class_schedule';
	protected $fillable = ['dayOfWeek', 'startTime', 'endTime', 'teacherId'];
}
