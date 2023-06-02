<?php

namespace App\Http\Requests\Billing;

use App\Http\Requests\RequestValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    use RequestValidationTrait;
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
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required'],
            'received_amount' => ['required'],
            'paid_by' => ['required'],
        ];
        if ($this->has('type')) {
            if ($this->type == 3) {
                $rules['month'] = ['required'];
                $rules['advanced_amount'] = ['required'];
            }
        }

        return $rules;
    }
}
