<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function var_dump;

class StoreVoucher extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'unique_code' => 'required|unique:vouchers,unique_code,'. $this->route('voucher')?->id .'|max:16',
            'amount' => 'required|integer|numeric|min:1',
            'expired_dt' => 'required|date_format:Y-m-d H:i:s|after:now',
            'used_dt' => 'date_format:Y-m-d H:i:s|nullable',
        ];
    }
}
