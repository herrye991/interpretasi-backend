<?php

namespace App\Http\Resources\Articles;

use Illuminate\Http\Resources\Json\JsonResource;

class Index extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'image' => $this->image,
            'viewers' => $this->viewers,
            'comments' => count($this->comments),
            'likes' => count($this->likes),
            'created_at' => $this->created_at,
        ];
    }
}
