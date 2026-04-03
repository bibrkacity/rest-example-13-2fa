<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'int|min:1',
            'per_page' => 'int|min:0',
            'sort_dir' => 'in:asc,desc',
            'sort_name' => 'string',
            'query' => 'string',
        ];
    }
}
