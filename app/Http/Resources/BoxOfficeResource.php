<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoxOfficeResource extends JsonResource
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
            'business' => BusinessResource::make($this->business),
            'name' => $this->name,
            'service'=> $this->service,
            'bank_data'=> BankDataResource::make($this->bankData),
            'isActive'=> $this->isActive
        ];
    }
}
