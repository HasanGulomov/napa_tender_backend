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
        'category'     => 'nullable|string',
        'budgetRange'  => 'nullable|string', 
        'closingDate'  => 'nullable|string', 
        'region'       => 'nullable|string',
        'source'       => 'nullable|string',
        'per_page'     => 'nullable|integer|min:1|max:100',
    ];
    }
}