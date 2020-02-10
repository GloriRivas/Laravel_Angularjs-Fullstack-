<?php

namespace App\Http\Controllers\Royex\Payroll;

use App\Repositories\Royex\PayrollRepository;

use App\Models2\Royex\SalaryDetailsToAllowance;

use App\libraries\Royex\Enumerations\LeaveStatus;

use App\Models2\Royex\CompanyAddressSetting;

use App\Models2\Royex\SalaryDetailsToLeave;

use App\Models2\Royex\SalaryDetailsToDeduction;

use App\Repositories\Royex\CommonRepository;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade as PDF;

use Illuminate\Support\Facades\DB;

use App\Models2\Royex\LeaveApplication;

use App\Models2\Royex\PrintHeadSetting;

use Illuminate\Http\Request;

use App\Models2\Royex\SalaryDetails;

use App\Models2\Royex\Employee;

use Carbon\Carbon;


class GenerateSalarySheet extends Controller
{

    protected $commonRepository;
    protected $payrollRepository;

    public function __construct(CommonRepository $commonRepository,PayrollRepository $payrollRepository){
        $this->commonRepository     = $commonRepository;
        $this->payrollRepository    = $payrollRepository;
    }


    public function index(Request $request){
        $results = SalaryDetails::with(['employee' => function($query){
            $query->with(['department','payGrade','hourlySalaries']);
        }])->orderBy('salary_details_id','DESC')->paginate(10);
        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'payGrade', 'hourlySalaries']);
            }])->orderBy('salary_details_id', 'DESC');

            if($request->monthField !=''){
                $results->where('month_of_salary',$request->monthField);
            }

            if($request->status !=''){
                $results->where('status',$request->status);
            }

            $results = $results->paginate(10);

            return   View('admin.payroll.salarySheet.pagination', compact('results'))->render();
        }
        $departmentList = $this->commonRepository->departmentList();
        return view('admin.payroll.salarySheet.salaryDetails',['results' =>$results,'departmentList'=>$departmentList]);
    }



    public function monthSalary(Request $request){
        $results = SalaryDetails::with(['employee'=>function($query){
            $query->with('payGrade');
        }])->where('month_of_salary', $request->month)->get();

        return view('admin.payroll.salarySheet.salaryDetails',['results' =>$results]);
    }



    public function create() {
        $employeeList = $this->commonRepository->employeeList();
        return view('admin.payroll.salarySheet.generateSalarySheet',['employeeList' =>$employeeList]);
    }



    public function calculateEmployeeSalary(Request $request){

        $query =  DB::select("SELECT temp.* FROM (
                    SELECT DATE_FORMAT(date,'%Y-%m') AS yearAndMonth,royex_view_employee_in_out_data.finger_print_id,royex_employee.employee_id FROM royex_view_employee_in_out_data
                    JOIN royex_employee ON royex_employee.finger_id = royex_view_employee_in_out_data.finger_print_id
                    ) AS temp WHERE yearAndMonth='$request->month' AND employee_id = $request->employee_id");

        if(count($query) <= 0){
            return redirect('generateSalarySheet/create')->with('error', 'No attendance found.');
        }

         $queryResult = SalaryDetails::where('employee_id',$request->employee_id)->where('month_of_salary',$request->month)->count();
         if($queryResult > 0){
             return redirect('generateSalarySheet')->with('error', 'Salary already generated for this month.');
         }

        $employeeList    = $this->commonRepository->employeeList();
        $employeeDetails = Employee::with('payGrade','hourlySalaries','department','designation')->where('employee_id',$request->employee_id)->first();
        if($employeeDetails->pay_grade_id !=0) {
            $employeeAllInfo = [];
            $allowance = [];
            $deduction = [];
            $tax = 0;

            $from_date = $request->month."-01" ;
            $to_date = date('Y-m-t',strtotime($from_date));

            $leaveRecord =  LeaveApplication::select('royex_leave_type.leave_type_id','leave_type_name','number_of_day','application_from_date','application_to_date')
                             ->join('royex_leave_type', 'royex_leave_type.leave_type_id','royex_leave_application.leave_type_id')
                            ->where('status',LeaveStatus::$APPROVE)
                            ->where('application_from_date','>=',$from_date)
                            ->where('application_to_date','<=',$to_date)
                            ->where('employee_id',$request->employee_id)
                            ->get();

            $monthAndYear    = explode('-',$request->month);
            $start_year          = $monthAndYear[0].'-01';
            $end_year            = $monthAndYear[0].'-12';

            $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
                            ->where('status',1)
                            ->where('employee_id',$request->employee_id)
                            ->whereBetween('month_of_salary',[$start_year,$end_year])
                            ->first();

            $allowance = $this->payrollRepository->calculateEmployeeAllowance($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);

            $deduction = $this->payrollRepository->calculateEmployeeDeduction($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);
            $tax = $this->payrollRepository->calculateEmployeeTax(
                $employeeDetails->payGrade->gross_salary,
                $employeeDetails->payGrade->basic_salary,
                $employeeDetails->date_of_birth,
                $employeeDetails->gender,
                $employeeDetails->pay_grade_id
            );
            $employeeAllInfo = $this->payrollRepository->getEmployeeOtmAbsLvLtAndWokDays(
                $request->employee_id, $request->month,
                $employeeDetails->payGrade->overtime_rate,
                $employeeDetails->payGrade->basic_salary
            );

            $data = [
                'employeeList'      => $employeeList,
                'allowances'        => $allowance,
                'deductions'        => $deduction,
                'tax'               => $tax['monthlyTax'],
                'taxAbleSalary'     => $tax['taxAbleSalary'],
                'employee_id'       => $request->employee_id,
                'month'             => $request->month,
                'employeeAllInfo'   => $employeeAllInfo,
                'employeeDetails'   => $employeeDetails,
                'leaveRecords'      => $leaveRecord,
                'financialYearTax'  => $financialYearTax,
                'employeeGrossSalary'  => $employeeDetails->payGrade->gross_salary,
            ];
        }else{
            $employeeHourlySalary = $this->payrollRepository->getEmployeeHourlySalary($request->employee_id, $request->month,$employeeDetails->hourlySalaries->hourly_rate);

            $data = [
                'employeeList'          => $employeeList,
                'hourly_rate'           => $employeeDetails->hourlySalaries->hourly_rate,
                'employee_id'           => $request->employee_id,
                'month'                 => $request->month,
                'totalWorkingHour'      => $employeeHourlySalary['totalWorkingHour'],
                'totalSalary'           => $employeeHourlySalary['totalSalary'],
                'employeeDetails'       => $employeeDetails,

            ];

            return view('admin.payroll.salarySheet.generateHourlySalarySheet',$data);
        }
        return view('admin.payroll.salarySheet.generateSalarySheet',$data);
    }



    public function store(Request $request){
        $input = $request->all();
        $input['created_by']  =  Auth::user()->user_id;
        $input['updated_by']  =  Auth::user()->user_id;


        try{
            DB::beginTransaction();

                $parentData = SalaryDetails::create($input);
                $employeeSalaryDetailsToAllowance  = $this->makeEmployeeSalaryDetailsToAllowanceDataFormat($request->all(),$parentData->salary_details_id);

                if(count($employeeSalaryDetailsToAllowance) > 0) {
                    SalaryDetailsToAllowance::insert($employeeSalaryDetailsToAllowance);
                }

                $employeeSalaryDetailsToDeduction  = $this->makeEmployeeSalaryDetailsToDeductionDataFormat($request->all(),$parentData->salary_details_id);
                if(count($employeeSalaryDetailsToDeduction) > 0) {
                    SalaryDetailsToDeduction::insert($employeeSalaryDetailsToDeduction);
                }

                $employeeSalaryDetailsToLeave  = $this->makeEmployeeSalaryDetailsToLeaveDataFormat($request->all(),$parentData->salary_details_id);
                if(count($employeeSalaryDetailsToLeave) > 0) {
                    SalaryDetailsToLeave::insert($employeeSalaryDetailsToLeave);
                }

            DB::commit();
            $bug = 0;
        }
        catch(\Exception $e){
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            return redirect('generateSalarySheet')->with('success', 'Salary Generate successfully.');
        }else {
            return redirect('generateSalarySheet')->with('error', 'Something Error Found !, Please try again.');
        }

    }


    public function makeEmployeeSalaryDetailsToAllowanceDataFormat($data,$salary_details_id){
        $allowanceData = [];
        if(isset($data['allowance_id'])) {
            for ($i=0; $i < count($data['allowance_id']); $i++) {
                $allowanceData[$i] =[
                    'salary_details_id'    => $salary_details_id,
                    'allowance_id'         => $data['allowance_id'][$i],
                    'amount_of_allowance'  => $data['amount_of_allowance'][$i],
                    'created_at'           => Carbon::now(),
                    'updated_at'           => Carbon::now(),
                ];
            }
        }
        return $allowanceData;
    }



    public function makeEmployeeSalaryDetailsToDeductionDataFormat($data,$salary_details_id){
        $deductionData= [];
        if(isset($data['deduction_id'])) {
            for ($i=0; $i < count($data['deduction_id']); $i++) {
                $deductionData[$i] =[
                    'salary_details_id'    => $salary_details_id,
                    'deduction_id'         => $data['deduction_id'][$i],
                    'amount_of_deduction'  => $data['amount_of_deduction'][$i],
                    'created_at'           => Carbon::now(),
                    'updated_at'           => Carbon::now(),
                ];
            }
        }
        return $deductionData;
    }



    public function makeEmployeeSalaryDetailsToLeaveDataFormat($data,$salary_details_id){
        $leaveData= [];
        if(isset($data['num_of_day'])) {
            for ($i=0; $i < count($data['num_of_day']); $i++) {
                $leaveData[$i] =[
                    'salary_details_id'    => $salary_details_id,
                    'num_of_day'           => $data['num_of_day'][$i],
                    'leave_type_id'        => $data['leave_type_id'][$i],
                    'created_at'           => Carbon::now(),
                    'updated_at'           => Carbon::now(),
                ];
            }
        }
        return $leaveData;
    }



    public function makePayment(Request $request){
        $data['status']         = 1;
        $data['comment']        = $request->comment;
        $data['payment_method'] = $request->payment_method;
        $data['created_by']    = Auth::user()->user_id;
        $data['updated_by']     = Auth::user()->user_id;
        $data['created_at']     = Carbon::now();
        $data['updated_at']     = Carbon::now();
        try{
            SalaryDetails::where('salary_details_id', $request->salary_details_id)->update($data);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            echo "success";
        }else {
           echo "error";
        }
    }



    public function generatePayslip($id){
        $paySlipId  = $id;
        $ifHourly  = SalaryDetails::with(['employee'=>function($q){
            $q->with(['hourlySalaries','department','designation']);
        }])->where('salary_details_id',$paySlipId)->first();

        if($ifHourly->action == 'monthlySalary'){
            $paySlipDataFormat     = $this->paySlipDataFormat($paySlipId);
        }else{
            $companyAddress            = CompanyAddressSetting::first();
            $data = [
                'salaryDetails'            => $ifHourly,
                'companyAddress'           => $companyAddress,
                'paySlipId'                 => $id,
            ];
            return view('admin.payroll.salarySheet.hourlyPaySlip',$data);
        }

        return view('admin.payroll.salarySheet.monthlyPaySlip',$paySlipDataFormat);
    }



    public function paySlipDataFormat($id){
        $printHeadSetting         = PrintHeadSetting::first();
        $salaryDetails            = SalaryDetails::select('royex_salary_details.*','royex_employee.employee_id','royex_employee.department_id','royex_employee.designation_id','royex_department.department_name','royex_designation.designation_name','royex_employee.first_name','royex_employee.last_name','royex_pay_grade.pay_grade_name','royex_employee.date_of_joining')
                                    ->join('royex_employee', 'royex_employee.employee_id','royex_salary_details.employee_id')
                                    ->join('royex_department', 'royex_department.department_id','royex_employee.department_id')
                                    ->join('royex_designation', 'royex_designation.designation_id','royex_employee.designation_id')
                                    ->join('royex_pay_grade', 'royex_pay_grade.pay_grade_id','royex_employee.pay_grade_id')
                                    ->where('salary_details_id',$id)->first();

        $salaryDetailsToAllowance = SalaryDetailsToAllowance::join('royex_allowance', 'royex_allowance.allowance_id','royex_salary_details_to_allowance.allowance_id')
                                    ->where('salary_details_id',$id)->get();

        $salaryDetailsToDeduction = SalaryDetailsToDeduction::join('royex_deduction', 'royex_deduction.deduction_id','royex_salary_details_to_deduction.deduction_id')
                                    ->where('salary_details_id',$id)->get();

        $salaryDetailsToLeave       = SalaryDetailsToLeave::select('royex_salary_details_to_leave.*','royex_leave_type.leave_type_name')
                                    ->join('royex_leave_type', 'royex_leave_type.leave_type_id','royex_salary_details_to_leave.leave_type_id')
                                    ->where('salary_details_id',$id)->get();


        $monthAndYear    = explode('-',$salaryDetails->month_of_salary);
        $start_year      = $monthAndYear[0].'-01';
        $end_year        = $salaryDetails->month_of_salary;

        $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
                            ->where('status',1)
                            ->where('employee_id',$salaryDetails->employee_id)
                            ->whereBetween('month_of_salary',[$start_year,$end_year])
                            ->first();

        return $data = [
            'salaryDetails'            => $salaryDetails,
            'salaryDetailsToAllowance' => $salaryDetailsToAllowance,
            'salaryDetailsToDeduction' => $salaryDetailsToDeduction,
            'paySlipId'                => $id,
            'financialYearTax'         => $financialYearTax,
            'salaryDetailsToLeave'     => $salaryDetailsToLeave,
            'printHeadSetting'         => $printHeadSetting,
        ];
    }



    public function downloadPayslip($id){
        $payslipId = $id;
        $ifHourly  = SalaryDetails::with(['employee'=>function($q){
                        $q->with(['hourlySalaries','department','designation']);
                    }])->where('salary_details_id',$payslipId)->first();

        if($ifHourly->action == 'monthlySalary'){
            $result      =  $this->paySlipDataFormat($payslipId);
        }else{
            $printHeadSetting            = PrintHeadSetting::first();
            $data = [
                'salaryDetails'            => $ifHourly,
                'printHeadSetting'         => $printHeadSetting,
            ];
//          return view('admin.payroll.salarySheet.hourlyPaySlipPdf',$data);
            $pdf = PDF::loadView('admin.payroll.salarySheet.hourlyPaySlipPdf',$data);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("payslip.pdf");
        }

        $pdf = PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf',$result);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("payslip.pdf");
    }



    public function downloadMyPayroll(){
        $printHeadSetting            = PrintHeadSetting::first();
        $results = SalaryDetails::with(['employee'=>function($query){
            $query->with('payGrade');
        }])->where('status',1)->where('employee_id',session('logged_session_data.employee_id'))->orderBy('salary_details_id','DESC')->get();

        $data = [
            'printHead'=>$printHeadSetting,
            'results'=>$results,
        ];

        $pdf = PDF::loadView('admin.payroll.report.pdf.myPayrollPdf',$data);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("my-payroll-Pdf.pdf");

    }



    public function paymentHistory(Request $request){
        $results= '';
        if($request->month){
            $results = SalaryDetails::select('royex_salary_details.basic_salary', 'royex_salary_details.gross_salary', 'royex_salary_details.month_of_salary', DB::raw('CONCAT(COALESCE(royex_employee.first_name,\'\'),\' \',COALESCE(royex_employee.last_name,\'\')) AS fullName'),
                'royex_employee.photo', 'royex_pay_grade.pay_grade_name', 'royex_hourly_salaries.hourly_grade', 'royex_department.department_name')
                ->join('royex_employee', 'royex_employee.employee_id', 'royex_salary_details.employee_id')
                ->join('royex_department', 'royex_department.department_id', 'royex_employee.department_id')
                ->leftJoin('royex_pay_grade', 'royex_pay_grade.pay_grade_id', 'royex_employee.pay_grade_id')
                ->leftJoin('royex_hourly_salaries', 'royex_hourly_salaries.hourly_salaries_id', 'royex_employee.hourly_salaries_id')
                ->where('royex_salary_details.status', 1)
                ->where('royex_salary_details.month_of_salary', $request->month)
                ->orderBy('salary_details_id', 'DESC')
                ->get();
        }

        return view('admin.payroll.report.paymentHistory',['results' =>$results,'month'=>$request->month]);
    }



    public function myPayroll() {
        $results = SalaryDetails::with(['employee'=>function($query){
            $query->with('payGrade');
        }])->where('status',1)->where('employee_id',session('logged_session_data.employee_id'))->orderBy('salary_details_id','DESC')->get();
        return view('admin.payroll.report.myPayroll',['results' =>$results]);
    }



}
