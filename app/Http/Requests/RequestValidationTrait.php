<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;

trait RequestValidationTrait {
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        $response = [
            'success' => FALSE,
            'data'    => NULL,
            'message' => $validator->errors()->all()
        ];
        throw new HttpResponseException(response()->json($response, 200));
    }
}
