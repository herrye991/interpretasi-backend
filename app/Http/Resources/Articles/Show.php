<?php

namespace App\Http\Resources\Articles;

use Illuminate\Http\Resources\Json\JsonResource;

class Show extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'photo' => $this->user->photo,
                'bio' => $this->user->bio,
            ],
            'category_id' => $this->category_id,
            'title' => $this->title,
            'url' => $this->url,
            'image' => $this->image,
            'content' => $this->content,
            'original_content' => $this->original_content,
            'tags' => $this->tags,
            'viewers' => $this->viewers,
            'comments' => count($this->comments),
            'likes' => count($this->likes),
            'created_at' => $this->created_at,
        ];
        return $response;
    }
}
