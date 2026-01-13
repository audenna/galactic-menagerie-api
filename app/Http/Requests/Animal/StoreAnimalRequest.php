<?php

namespace App\Http\Requests\Animal;

use App\Rules\ValidateNameRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                new ValidateNameRule(
                    50,
                    3,
                    'animals', // table to check uniqueness
                    [
                        'species' => null,
                        'preferred_environment' => null,
                        'enclosure_id' => null,
                    ]
                )
            ],
            'species' => ['required', 'string', 'max:100'],
            'preferred_environment' => ['required', 'string', 'max:100'],
            'enclosure_id' => ['required', 'exists:enclosures,id'],
        ];
    }
}
