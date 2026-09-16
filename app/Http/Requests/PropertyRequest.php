<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'transaction_type' => ['required', 'in:sale,rent'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_type.required' => 'เลือกประเภท ขาย หรือ เช่า',
            'transaction_type.in' => 'เลือกประเภท ขาย หรือ เช่า',
            'name.required' => 'กรอกชื่อโครงการหรือชื่อทรัพย์',
            'price.required' => 'กรอกราคา',
            'price.numeric' => 'ราคาต้องเป็นตัวเลข',
            'price.min' => 'ราคาต้องไม่น้อยกว่า 0',
            'bedrooms.required' => 'กรอกจำนวนห้องนอน',
            'bedrooms.integer' => 'ห้องนอนต้องเป็นจำนวนเต็ม',
            'bedrooms.min' => 'ห้องนอนต้องไม่น้อยกว่า 0',
            'bedrooms.max' => 'ห้องนอนต้องไม่เกิน 50',
            'location.required' => 'กรอกทำเล',
        ];
    }
}
