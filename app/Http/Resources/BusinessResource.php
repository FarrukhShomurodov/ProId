<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
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
            'image' => $this->image,
            'inn' => $this->inn,
            'name_of_business' => $this->name_of_business,
            'form_of_business' => $this->form_of_business,
            'oked' => $this->oked,
            'address' => $this->address,
        ];
    }
}
