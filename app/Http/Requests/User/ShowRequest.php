<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        /**
         * Determine if the $this->user is authorized to show user model.
         */
        return true;
    }
}
