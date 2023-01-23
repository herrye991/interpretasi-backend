<?php

namespace App\Http\Resources\Articles;

use Illuminate\Http\Resources\Json\JsonResource;
use URL;

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
        // URL GENERATION
        $url = str_replace(' ', '-', $this->title);
        $url = preg_replace('/[^A-Za-z0-9\-]/', '', $url);
        $url = preg_replace('/-+/', '-', $url);
        $url = strtolower($url);
        $url = URL::current() . '/' . $url . '?uid=' . $this->uniqid;
        $response = [
            'title' => $this->title,
            'url' => URL::current() . '/' . $this->url,
            'thumbnail' => $this->thumbnail,
            'viewers' => $this->viewers,
            'comments' => count($this->comments),
            'likes' => count($this->likes),
            'created_at' => $this->created_at,
        ];
        return $response;
    }
}
