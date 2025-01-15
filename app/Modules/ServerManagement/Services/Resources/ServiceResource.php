<?php

namespace App\Modules\ServerManagement\Services\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'category' => $this->whenLoaded('category', fn() => $this->category->name),
            'service_category_id' => $this->service_category_id,
            'description' => $this->description,
            'link' => $this->link
        ];
    }
}
