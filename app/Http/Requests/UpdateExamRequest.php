<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handle authorization in policies if needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $exam = $this->route('exam');

        return [
            'maktab_id' => ['sometimes', 'exists:maktabs,id'],
            'sinf_id' => ['sometimes', 'exists:sinfs,id'],
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'teacher_id' => ['sometimes', 'exists:teachers,id'],
            'teacher2_id' => ['nullable', 'exists:teachers,id'],
            'type' => ['sometimes', 'string'],
            'serial_number' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::unique('exams')
                    ->where('sinf_id', $this->sinf_id ?? $exam->sinf_id)
                    ->where('subject_id', $this->subject_id ?? $exam->subject_id)
                    ->where('type', $this->type ?? $exam->type)
                    ->ignore($exam->id)
            ],
            'quarter' => ['nullable', 'in:I,II,III,IV'],
            'metod_id' => ['sometimes', 'exists:teachers,id'],
            'problems' => ['nullable', 'array'],
            'problems.*.id' => ['required_with:problems', 'integer'],
            'problems.*.max_mark' => ['required_with:problems', 'numeric', 'min:0'],

        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'serial_number.unique' => 'Bu sinf, fan va imtihon turi uchun bunday tartib raqamli imtihon allaqachon mavjud.',
            'quarter.in' => 'Chorak qiymati I, II, III yoki IV bo\'lishi kerak.',
        ];
    }
}
