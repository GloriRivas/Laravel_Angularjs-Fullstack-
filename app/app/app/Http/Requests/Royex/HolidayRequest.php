<?php

namespace App\Http\Requests\Royex;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(isset($this->manageHoliday)){
            return [
                'holiday_name'  => 'required|unique:royex_holiday,holiday_name,'.$this->manageHoliday.',holiday_id'
            ];
        }
        return [
            'holiday_name'=>'required|unique:royex_holiday',
        ];

    }
}
