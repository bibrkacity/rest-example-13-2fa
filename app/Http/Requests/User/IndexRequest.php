<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseIndexRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends BaseIndexRequest
{
    #[\Override]
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['email'] = 'email';
        $rules['sort_name'][] = Rule::in(['name', 'email', 'created_at', 'updated_at']);

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
