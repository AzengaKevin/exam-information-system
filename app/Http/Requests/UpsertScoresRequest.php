<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertScoresRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check whether it is the correct teacher teaching the subjects
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
            'grading_id' => ['nullable', 'integer'],
            'scores' => ['required', 'array'],
            'scores.*.score' => ['nullable', 'integer'],
        ];
    }
}
