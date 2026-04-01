<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
{
    return [
    'id' => $this->id,
    'name' => $this->name,
    'description' => $this->description,
    'deadline' => $this->deadline,
    'creator_id' => $this->creator_id,
    'created_at' => $this->created_at,

   


    'creator' => $this->creator ? [
    'id' => $this->creator->id,
    'name' => $this->creator->name,
] : null,




    'role' => $this->whenPivotLoaded('project_user', function () {
        return $this->pivot->role;
    }),
];






}
}