<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'search'      => 'nullable|string',

            'regionId'   => 'nullable|array',
            'regionId.*' => 'integer|in:1,2,3,4,5,6,7,8,9,10,11,12',

            'categoryId'   => 'nullable|array',
            'categoryId.*' => 'integer|in:1,2,3,4',

            'sourceId'   => 'nullable|array',
            'sourceId.*' => 'integer|in:1,2,3,4',

            'minBudget'    => 'nullable|numeric|min:0',
            'maxBudget'    => 'nullable|numeric|min:0',
            'closingDate'   => 'nullable|date_format:Y-m-d',
        ];
    }
}
