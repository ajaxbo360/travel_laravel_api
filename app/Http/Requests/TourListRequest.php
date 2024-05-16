<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priceFrom' => 'numeric',
            'priceTo' => 'numeric',
            'dateFrom' => 'date',
            'dateTo' => 'date',
            'sortBy' => Rule::in(['price']),
            'sortOrder' => Rule::in(['asc', 'desc']),
        ];
    }

    public function messages(): array
    {
        return [
            'sortBy' => 'sortBy must be "price"',
            'sortOrder' => 'sortOrder must be "asc" or "desc"',
            'priceFrom.numeric' => 'priceFrom must be numeric',
            'priceTo.numeric' => 'priceTo must be numeric',
            'dateFrom.date' => 'dateFrom must be date',
            'dateTo.date' => 'dateTo must be date',
        ];
    }
}
