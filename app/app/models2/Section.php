<?php

namespace App\Models2;

use Illuminate\Database\Eloquent\Model;

class Section extends Model {
	public $timestamps = false;
	protected $table = 'sections';

	public static function getSectionsOfClassTeacher($teacher_id) {
		return self::where("classTeacherId", 'LIKE', '%"' . $teacher_id . '"%')->pluck('id');
	}
}
