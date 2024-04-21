<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankDataResource extends JsonResource
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
            'name_of_banking_akkaunt' => $this->name_of_banking_akkaunt,
            'business_id' => BusinessResource::make($this->business),
            'mfo' => $this->mfo,
            'payment_account' => $this->payment_account,
        ];
    }
}
