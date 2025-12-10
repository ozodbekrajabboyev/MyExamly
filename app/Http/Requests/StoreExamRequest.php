<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExamRequest extends FormRequest
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
        return [
            'maktab_id' => ['required', 'exists:maktabs,id'],
            'sinf_id' => ['required', 'exists:sinfs,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'teacher2_id' => ['nullable', 'exists:teachers,id'],
            'type' => ['required', 'string'],
            'serial_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('exams')
                    ->where('sinf_id', $this->sinf_id)
                    ->where('subject_id', $this->subject_id)
                    ->where('type', $this->type)
                    ->ignore($this->route('exam'))
            ],
            'quarter' => ['nullable', 'in:I,II,III,IV'],
            'metod_id' => ['required', 'exists:teachers,id'],
            'problems' => ['nullable', 'array'],
            'problems.*.id' => ['required', 'integer'],
            'problems.*.max_mark' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:pending,approved,rejected'],
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
