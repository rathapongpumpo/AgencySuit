<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'เลือกลูกค้า', 'property_id.required' => 'เลือกทรัพย์',
            'appointment_date.required' => 'เลือกวันที่นัดดู', 'appointment_time.required' => 'เลือกเวลานัดดู',
            'appointment_time.date_format' => 'เวลาไม่ถูกต้อง', 'note.max' => 'หมายเหตุต้องไม่เกิน 300 ตัวอักษร',
        ];
    }
}
