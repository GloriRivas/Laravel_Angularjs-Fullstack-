<?php

namespace App\Http\Controllers\Royex\Leave;

use App\Http\Requests\Royex\PublicHolidayRequest;

use App\Repositories\Royex\CommonRepository;

use App\Http\Controllers\Controller;

use App\Models2\Royex\HolidayDetails;

use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository){
        $this->commonRepository = $commonRepository;
    }


    public function index(){
        $results = HolidayDetails::with('holiday')->orderBy('holiday_details_id', 'desc')->get();
        return view('admin.leave.publicHoliday.index',['results'=>$results]);
    }


    public function create(){
        $holidayList = $this->commonRepository->holidayList();
        return view('admin.leave.publicHoliday.form',['holidayList' => $holidayList]);
    }


    public function store(PublicHolidayRequest $request){
        $input              = $request->all();
        $input['from_date'] = dateConvertFormtoDB($input['from_date']);
        $input['to_date']   = dateConvertFormtoDB($input['to_date']);
        try{
            HolidayDetails::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            return redirect('publicHoliday')->with('success', 'Public holiday successfully saved.');
        }else {
            return redirect('publicHoliday')->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function edit($id){
        $holidayList    = $this->commonRepository->holidayList();
        $editModeData   = HolidayDetails::findOrFail($id);
        return view('admin.leave.publicHoliday.form',['editModeData' => $editModeData,'holidayList'=>$holidayList]);
    }


    public function update(PublicHolidayRequest $request,$id) {
        $holidayDetails     = HolidayDetails::findOrFail($id);
        $input              = $request->all();
        $input['from_date'] = dateConvertFormtoDB($input['from_date']);
        $input['to_date']   = dateConvertFormtoDB($input['to_date']);
        try{
            $holidayDetails->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            return redirect()->back()->with('success', 'Public holiday successfully updated. ');
        }else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function destroy($id){
        try{
            $holidayDetails = HolidayDetails::findOrFail($id);
            $holidayDetails->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

}
