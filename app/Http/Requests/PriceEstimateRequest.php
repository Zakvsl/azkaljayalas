<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceEstimateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Already handled by auth middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_type' => ['required', 'string', 'in:canopy,fence,gate,railing,stairs,truss'],
            'material_type' => ['required', 'string', 'in:stainless_steel,mild_steel,galvanized_steel,aluminum'],
            'dimensions' => ['required', 'array'],
            'dimensions.length' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'dimensions.width' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'dimensions.thickness' => ['required', 'numeric', 'min:0.1', 'max:500'],
            'additional_features' => ['sometimes', 'array'],
            'additional_features.*' => ['string', 'in:painting,welding,installation,polishing,design'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'dimensions.length' => 'length',
            'dimensions.width' => 'width',
            'dimensions.thickness' => 'thickness',
            'additional_features.*' => 'additional feature',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dimensions.length.max' => 'The length cannot exceed 100 meters.',
            'dimensions.width.max' => 'The width cannot exceed 100 meters.',
            'dimensions.thickness.max' => 'The thickness cannot exceed 500 millimeters.',
            'additional_features.*.in' => 'The selected additional feature is invalid.',
        ];
    }
}