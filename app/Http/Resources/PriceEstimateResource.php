<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceEstimateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_type' => $this->project_type,
            'material_type' => $this->material_type,
            'dimensions' => $this->dimensions,
            'additional_features' => $this->additional_features,
            'estimated_price' => $this->estimated_price,
            'actual_price' => $this->actual_price,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'formatted_estimated_price' => 'Rp ' . number_format($this->estimated_price, 0, ',', '.'),
            'formatted_actual_price' => $this->actual_price ? 'Rp ' . number_format($this->actual_price, 0, ',', '.') : null,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
        ];
    }
}