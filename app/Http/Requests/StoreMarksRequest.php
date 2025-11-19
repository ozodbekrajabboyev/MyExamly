<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Changed to true to allow authorization
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxMark = $this->input('max_mark', 100); // Get max_mark from request or default to 100

        return [
            'mark' => [
                'required',
                'numeric',
                'min:0',
                "max:{$maxMark}"
            ],
            'student_id' => 'required|exists:students,id',
            'exam_id' => 'required|exists:exams,id',
            'problem_id' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom validation messages in Uzbek
     */
    public function messages(): array
    {
        return [
            'mark.required' => 'Baho kiritish majburiy.',
            'mark.numeric' => 'Baho raqam bo\'lishi kerak.',
            'mark.min' => 'Baho 0 dan kam bo\'lmasligi kerak.',
            'mark.max' => 'Baho :max dan oshmasligi kerak.',
            'student_id.required' => 'O\'quvchi ID si majburiy.',
            'student_id.exists' => 'Bunday o\'quvchi topilmadi.',
            'exam_id.required' => 'Imtihon ID si majburiy.',
            'exam_id.exists' => 'Bunday imtihon topilmadi.',
            'problem_id.required' => 'Masala ID si majburiy.',
            'problem_id.integer' => 'Masala ID si butun son bo\'lishi kerak.',
            'problem_id.min' => 'Masala ID si 1 dan kam bo\'lmasligi kerak.',
        ];
    }

    /**
     * Static method to get validation rules for a specific max mark
     */
    public static function getMarkRules(int $maxMark): array
    {
        return [
            'required',
            'numeric',
            'min:0',
            "max:{$maxMark}"
        ];
    }
}
