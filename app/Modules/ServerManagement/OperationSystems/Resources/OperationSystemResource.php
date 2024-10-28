<?php

namespace App\Modules\ServerManagement\OperationSystems\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OperationSystemResource extends JsonResource
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
            'versions' => VersionResource::collection($this->versions),
            'image' => $this->image ? asset('storage' . $this->image) : asset('default.jpg'),
            'server_types' => ServerTypeResource::collection($this->serverTypes),
            'status' => $this->status,
            'checked' => false
        ];
    }
}
