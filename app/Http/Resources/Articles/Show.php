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
            'title' => $this->title,
            'url' => $this->url,
            'image' => $this->image,
            'content' => $this->content,
            'cateogies' => $this->categories,
            'viewers' => $this->viewers,
            'comments' => count($this->comments),
            'likes' => count($this->likes),
            'created_at' => $this->created_at,
        ];
        return $response;
    }
}
