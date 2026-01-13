<?php

namespace App\Http\Requests\Enclosure;

use App\Rules\ValidateNameRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnclosureRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                new ValidateNameRule(
                    50,
                    3,
                    'enclosures', // table to check uniqueness
                    ['type' => null]
                )
            ],
            'type'     => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];
    }
}
