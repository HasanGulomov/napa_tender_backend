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
            'region_id'   => 'nullable|array',
            'region_id.*' => 'integer|in:1,2,3,4,5,6,7,8,9,10,11,12',

            'category_id'   => 'nullable|array',
            'category_id.*' => 'integer|in:1,2,3,4',

            'source_id'   => 'nullable|array',
            'source_id.*' => 'integer|in:1,2,3,4',

            'min_budget'    => 'nullable|numeric|min:0',
            'max_budget'    => 'nullable|numeric|gt:min_budget',
            'closingDate'   => 'nullable|date_format:Y-m-d',
        ];
    }
}
