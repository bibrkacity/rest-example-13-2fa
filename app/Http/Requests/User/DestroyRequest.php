<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /**
         * Determine if the $this->user is authorized to destroy user model.
         */
        return true;
    }
}
