<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
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
            'job' => JobResource::make($this->job),
            'place' => $this->place,
            'post' => $this->post,
            'started' => $this->started,
            'expired' => $this->expired,
            'is_working' => $this->is_working
        ];
    }
}
