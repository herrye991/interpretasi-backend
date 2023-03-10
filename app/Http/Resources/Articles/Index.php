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
            'category_id' => $this->category_id,
            'title' => $this->title,
            'url' => $this->url,
            'image' => $this->image,
            'viewers' => $this->viewers,
            'comments_count' => count($this->comments),
            'likes_count' => count($this->likes),
            'created_at' => $this->created_at,
            'user' => $this->user
        ];
    }
}
