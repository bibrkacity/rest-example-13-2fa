<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'name' => 'string|max:255',
            'email' => 'string|email|unique:users,email,'.$id.',id|max:255',
            'password' => 'string|confirmed|min:6',
            'password_confirmation' => 'required_with:password|string|same:password',
        ];
    }

    public function authorize(): bool
    {
        /**
         * Determine if the $this->user is authorized to update user model.
         */
        return true;
    }
}
