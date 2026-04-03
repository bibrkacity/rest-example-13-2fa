<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    #[\Override]
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['email'] = 'email';

        return $rules;
    }

    public function authorize(): bool
    {
        /**
         * Determine if the $this->user is authorized to get index of user models.
         */
        return true;
    }
}
