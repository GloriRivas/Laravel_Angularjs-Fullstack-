<?php

namespace App\Repositories\Royex;

use App\Models2\Royex\PerformanceCategory;

use Illuminate\Support\Facades\DB;

use App\Models2\Royex\TrainingType;

use App\Models2\Royex\WorkShift;

use App\Models2\Royex\PayGrade;

use App\Models2\Royex\Employee;

use App\Models2\Royex\Role;


class CommonRepository
{

    public function roleList(){
        $results = Role::get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->role_id] = $value->role_name;
        }
        return $options ;
    }


    public function userList(){
        $results = DB::table('royex_user')->where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->user_id] = $value->user_name;
        }
        return $options ;
    }


    public function departmentList(){
        $results = DB::table('royex_department')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->department_id] = $value->department_name;
        }
        return $options ;
    }


    public function designationList(){
        $results = DB::table('royex_designation')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->designation_id] = $value->designation_name;
        }
        return $options ;
    }


    public function branchList(){
        $results = DB::table('royex_branch')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->branch_id] = $value->branch_name;
        }
        return $options ;
    }


    public function supervisorList(){
        $results = DB::table('royex_employee')->where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->last_name;
        }
        return $options ;
    }


    public function holidayList(){
        $results = DB::table('royex_holiday')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->holiday_id] = $value->holiday_name;
        }
        return $options ;
    }


    public function weekList(){
        $results = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value] = $value;
        }
        return $options ;
    }


    public function leaveTypeList(){
        $results = DB::table('royex_leave_type')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->leave_type_id] = $value->leave_type_name;
        }
        return $options ;
    }


    public function workShiftList(){
        $results = WorkShift::get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->work_shift_id] = $value->work_shift_name;
        }
        return $options ;
    }


    public function employeeList(){
        $results = Employee::where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->last_name;
        }
        return $options ;
    }

    public function performanceCategoryList(){
        $results = PerformanceCategory::all();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->performance_category_id] = $value->performance_category_name;
        }
        return $options ;
    }

    public function getEmployeeInfo($id){
        return  Employee::where('user_id',$id)->first();
    }


    public function getEmployeeDetails($id){
        return  Employee::where('employee_id',$id)->first();
    }

    public function trainingTypeList(){
        $results = TrainingType::where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->training_type_id] = $value->training_type_name;
        }
        return $options ;
    }

    public function payGradeList(){
        $results = PayGrade::all();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->pay_grade_id] = $value->pay_grade_name;
        }
        return $options ;
    }

}
