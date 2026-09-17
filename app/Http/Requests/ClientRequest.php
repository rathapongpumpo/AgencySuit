<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'transaction_type' => ['required', 'in:buy,rent'],
            'budget' => ['required', 'numeric', 'min:0'],
            'locations' => ['required', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'contact_channel' => ['nullable', 'string', 'max:100'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'minimum_size' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'transit_preference' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'กรอกชื่อหรือชื่อเล่นของลูกค้า',
            'name.max' => 'ชื่อลูกค้าต้องไม่เกิน 255 ตัวอักษร',
            'transaction_type.required' => 'เลือกว่าลูกค้าต้องการซื้อหรือเช่า',
            'transaction_type.in' => 'เลือกว่าลูกค้าต้องการซื้อหรือเช่า',
            'budget.required' => 'กรอกงบประมาณ',
            'budget.numeric' => 'งบประมาณต้องเป็นตัวเลข',
            'budget.min' => 'งบประมาณต้องไม่น้อยกว่า 0',
            'locations.required' => 'กรอกทำเลที่สนใจ',
            'phone.max' => 'เบอร์โทรต้องไม่เกิน 30 ตัวอักษร',
            'contact_channel.max' => 'ช่องทางติดต่อยาวเกินไป',
            'bedrooms.integer' => 'จำนวนห้องนอนต้องเป็นจำนวนเต็ม',
            'bedrooms.min' => 'จำนวนห้องนอนต้องไม่น้อยกว่า 0',
            'bedrooms.max' => 'จำนวนห้องนอนต้องไม่เกิน 50',
            'minimum_size.numeric' => 'ขนาดขั้นต่ำต้องเป็นตัวเลข',
            'minimum_size.min' => 'ขนาดขั้นต่ำต้องไม่น้อยกว่า 0',
            'transit_preference.max' => 'เงื่อนไขทำเลเพิ่มเติมยาวเกินไป',
            'notes.max' => 'หมายเหตุต้องไม่เกิน 2,000 ตัวอักษร',
        ];
    }
}
