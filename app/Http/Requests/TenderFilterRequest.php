<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * @queryParam region_id integer Viloyatni tanlang. Example: 1
     * @enumValues region_id {"1": "Tashkent", "2": "Andijan", "3": "Bukhara", "4": "Fergana", "5": "Jizzakh", "6": "Namangan", "7": "Navoi", "8": "Kashkadarya", "9": "Samarkand", "10": "Sirdaryo", "11": "Surkhandarya", "12": "Khorezm"}
     * * @queryParam category_id integer Kategoriyani tanlang. Example: 1
     * @enumValues category_id {"1": "Technology", "2": "Infrastructure", "3": "Supplies", "4": "Sustainability"}
     * * @queryParam source_id integer Manbani tanlang. Example: 1
     * @enumValues source_id {"1": "IT MARKET", "2": "UZEX", "3": "TENDER WEEK", "4": "XT-XARID"}
     * * @queryParam min_budget numeric Minimal budjet. Example: 1000000
     * @queryParam max_budget numeric Maksimal budjet. Example: 500000000
     * @queryParam closingDate date Tugash muddati (YYYY-MM-DD). Example: 2025-03-15
     */
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