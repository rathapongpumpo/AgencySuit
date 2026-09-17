<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'stage' => ['required', 'in:'.implode(',', array_keys(config('deals.stages', [])))],
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'co_agent_split' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'stage.required' => 'เลือกสถานะดีล', 'stage.in' => 'สถานะดีลไม่ถูกต้อง',
            'amount.numeric' => 'มูลค่าดีลต้องเป็นตัวเลข', 'amount.min' => 'มูลค่าดีลต้องไม่น้อยกว่า 0',
            'commission_rate.max' => 'คอมมิชชันต้องไม่เกิน 100%', 'co_agent_split.max' => 'ส่วนแบ่งโคเอเจนต์ต้องไม่เกิน 100%',
        ];
    }
}
