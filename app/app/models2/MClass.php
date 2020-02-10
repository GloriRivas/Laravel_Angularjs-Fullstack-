<?php

namespace App\Models2;

use App\Models2\AcademicYear;
use App\Models2\Section;
use App\Models2\User;
use Illuminate\Database\Eloquent\Model;

class MClass extends Model {
	public $timestamps = false;
	protected $table = 'classes';

	public static function getSectionsByClassId($class_id) {
		return Section::where('classId', $class_id)->get()->toArray();
	}

	public static function getSectionsIdsByClassesIds($classes_ids) {
		return Section::whereIn('classId', $classes_ids)->pluck('id');
	}

	public static function getClassesIdsByTeacherId($teacher_id) {
		return self::where('classTeacher', 'LIKE', '%"' . $teacher_id . '"%')
		  ->pluck('id');
	}

	public static function getSectionsIdsByClassTeacherId($class_teacher_id) {
		return Section::where('classTeacherId', 'LIKE', '%"' . $class_teacher_id . '"%')
		  ->pluck('id');
	}

	public static function getClassesIdsOfStudentsIds($students_ids) {
		$class_ids = User::where('role', 'student')
			->whereIn('id', $students_ids)
		  ->select('studentClass')
		  ->pluck('studentClass')
		  ->toArray();

		return $class_ids;
	}

	public static function getClassesIdsByParentId($parent_id) {
		// get students ids by parent id
		$students_ids = User::getStudentsIdsFromParentId($parent_id);

		// get class ids by students ids
		$classes_ids = self::getClassesIdsOfStudentsIds($students_ids);

		return $classes_ids;
	}

	public static function getClassesBySectionIds($sectionsIds) {
		$classesIds = Section::whereIn('id', $sectionsIds)->pluck('classId');
		$classes = self::whereIn('id', $classesIds);

		return $classes;
	}

	public static function getClassIdBySectionId($sectionId) {
		if(Section::where('id', $sectionId)->count()) {
			return Section::where('id', $sectionId)->first()->classId;
		} else {
			return 0;
		}
	}
}