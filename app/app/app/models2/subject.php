<?php

namespace App\Models2;

use App\Models2\MClass;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model {
	public $timestamps = false;
	protected $table = 'subject';

	public static function getSubjectsIdsByClassesIds($classes_ids) {
		$subjects_ids = [];

		$subjects = MClass::whereIn('id', $classes_ids)
		  ->select('classSubjects')
		  ->pluck('classSubjects')
		  ->toArray();

		foreach ($subjects as $key => $value) {
			foreach (json_decode($value) as $id) {
				if(!in_array($id, $subjects_ids)) {
					$subjects_ids[] = $id;
				}
			}
		}

		return $subjects_ids;
	}
}