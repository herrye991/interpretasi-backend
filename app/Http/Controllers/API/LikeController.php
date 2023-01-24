<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Helpers\ResponseFormatter;
use App\Models\Like;

class LikeController extends Controller
{
    function __construct()
    {
        $this->middleware('auth:api');
        $this->user = auth('api')->user();
    }

    public function index($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $like = Like::where('user_id', $this->user->id)->where('article_id', $article->id)->first();
        if (!is_null($like)) {
            return ResponseFormatter::success(true, 200, 200);
        } else {
            return ResponseFormatter::success(false, 200, 200);
        }
    }

    public function store($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $like = Like::where('user_id', $this->user->id)->where('article_id', $article->id)->first();
        if (is_null($like)) {
            Like::create([
                'user_id' => $this->user->id,
                'article_id' => $article->id
            ]);
            return ResponseFormatter::success('Liked Post', 200, 200);
        } else {
            $like->delete();
            return ResponseFormatter::success('Unliked Post', 200, 200);
        }
    }
}
