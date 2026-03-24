<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class Verify2faRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
