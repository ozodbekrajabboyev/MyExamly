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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxMark = $this->input('max_mark', 100);

        return [
            'mark' => [
                'required',
                'numeric',
                'min:0',
                "max:{$maxMark}",
                'regex:/^\d+(\.\d{1})?$/' // Allow up to 1 decimal place
            ],
            'student_id' => 'required|exists:students,id',
            'exam_id' => 'required|exists:exams,id',
            'problem_id' => 'required|integer|min:1',
            'max_mark' => 'nullable|numeric|min:0'
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
            'mark.regex' => 'Baho faqat 1 ta kasr raqamga ega bo\'lishi mumkin (masalan: 2.5).',
            'student_id.required' => 'O\'quvchi ID si kerak.',
            'student_id.exists' => 'O\'quvchi topilmadi.',
            'exam_id.required' => 'Imtihon ID si kerak.',
            'exam_id.exists' => 'Imtihon topilmadi.',
            'problem_id.required' => 'Topshiriq ID si kerak.',
            'problem_id.integer' => 'Topshiriq ID si butun son bo\'lishi kerak.',
            'problem_id.min' => 'Topshiriq ID si 1 dan kichik bo\'lmasligi kerak.'
        ];
    }

    /**
     * Get validation rules for a specific mark with max value
     */
    public static function getMarkRules(float $maxMark): array
    {
        return [
            'required',
            'numeric',
            'min:0',
            "max:{$maxMark}",
            'regex:/^\d+(\.\d{1})?$/' // Allow up to 1 decimal place
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'mark' => 'baho',
            'student_id' => 'o\'quvchi',
            'exam_id' => 'imtihon',
            'problem_id' => 'masala',
            'max_mark' => 'maksimal baho',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string decimal to float if needed
        if ($this->has('mark') && is_string($this->mark)) {
            $this->merge([
                'mark' => (float) $this->mark
            ]);
        }

        if ($this->has('max_mark') && is_string($this->max_mark)) {
            $this->merge([
                'max_mark' => (float) $this->max_mark
            ]);
        }
    }
}
