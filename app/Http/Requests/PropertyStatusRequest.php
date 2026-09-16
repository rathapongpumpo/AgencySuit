<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:'.implode(',', array_keys(config('properties.statuses', [])))],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'เลือกสถานะ',
            'status.in' => 'สถานะไม่ถูกต้อง',
        ];
    }
}
