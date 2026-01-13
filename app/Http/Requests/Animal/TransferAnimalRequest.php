<?php

namespace App\Http\Requests\Animal;

use Illuminate\Foundation\Http\FormRequest;

class TransferAnimalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'target_enclosure_id' => [
                'required',
                'integer',
                'exists:enclosures,id',
            ],
        ];
    }
}
