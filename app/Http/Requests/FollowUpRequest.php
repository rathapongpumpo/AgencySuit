<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'days' => ['nullable', 'integer', 'in:1,3,7'],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:300'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('days') && ! $this->filled('due_date')) {
                $validator->errors()->add('due_date', 'เลือกวันที่ต้องติดตาม');
            }
        });
    }

    public function messages(): array
    {
        return ['due_date.date' => 'วันที่ติดตามไม่ถูกต้อง', 'note.max' => 'หมายเหตุต้องไม่เกิน 300 ตัวอักษร'];
    }
}
