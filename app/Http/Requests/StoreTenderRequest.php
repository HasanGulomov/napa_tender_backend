<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTenderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    // StoreTenderRequest.php ichida

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'categoryId'  => 'required|integer|exists:categories,id',
            'regionId'    => 'required|integer|exists:regions,id',
            'sourceId'    => 'required|integer|exists:sources,id',
            'deadline'    => 'required|date',
            'budget'      => 'required|numeric',

            'category_id' => 'nullable',
            'region_id'   => 'nullable',
            'source_id'   => 'nullable',
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'category_id' => $this->categoryId,
            'region_id'   => $this->regionId,
            'source_id'   => $this->sourceId,
        ]);
    }
}
