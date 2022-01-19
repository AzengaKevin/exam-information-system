<?php

namespace App\Http\Requests;

use App\Models\Exam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
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
        return [
            'system.school_name' => ['bail', 'nullable', 'string'],
            'system.school_type' => ['bail', 'nullable', Rule::in(['boys', 'girls', 'mixed'])],
            'system.school_level' => ['bail', 'nullable', Rule::in(['primary', 'secondary'])],
            'system.school_has_streams' => ['bail', 'nullable'],
            'system.boarding_school' => ['bail', 'nullable'],

            'general.school_website' => ['bail', 'required', 'string'],
            'general.school_address' => ['bail', 'required', 'string'],
            'general.school_telephone_number' => ['bail', 'required', 'string'],
            'general.school_email_address' => ['bail', 'required', 'string'],
            'general.current_academic_year' => ['bail', 'required', 'int'],
            'general.school_manager_responsibility_id' => ['bail', 'required', 'int'],
            'general.exam_manager_responsibility_id' => ['bail', 'required', 'int'],
            'general.current_term' => ['bail', 'required', 'string', Rule::in(Exam::termOptions())],

            'raw.logo' => ['nullable', 'image', 'max:96']
        ];
    }
}
