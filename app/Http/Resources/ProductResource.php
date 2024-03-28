<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'stock' => $this->stock,
            'price' => $this->price,
            'status' => $this->status,
            'category' => $this->category->name,
            'created_by' => $this->user_creator->email,
            'updated_by' => $this->user_updater->email,
            'created_at' => (new DateTime($this->created_at))->format("Y-m-d H:i:s"),
            'updated_at' => (new DateTime($this->updated_at))->format("Y-m-d H:i:s"),
        ];
    }
}
