<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['type' => ['required', 'in:hard_to_use,bug,feature'], 'message' => ['required', 'string', 'max:300']];
    }

    public function messages(): array
    {
        return ['type.required' => 'เลือกประเภทความคิดเห็น', 'message.required' => 'กรอกความคิดเห็น', 'message.max' => 'ความคิดเห็นต้องไม่เกิน 300 ตัวอักษร'];
    }
}
