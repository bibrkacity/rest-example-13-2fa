<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required|string|same:password',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
