<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Http\Resources\Articles\Index as ArticleIndex;
use App\Models\Like;

class ArticleController extends Controller
{
    function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
        $this->user = auth('api')->user();
    }
    
    public function index(Article $article)
    {
        $type = request()->type;
        if ($type == 'categories') {
            return $this->categories();
        }
        
        $articles = $article->with(['comments', 'likes'])->get();
        return ArticleIndex::collection($articles);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $type = request()->type;
        // Like Check
        if ($type == 'like') {
            $like = Like::where('user_id', $this->user->id)->where('article_id', $article->id)->first();
            if (!is_null($like)) {
                return ResponseFormatter::success(true, 200, 200);
            } else {
                return ResponseFormatter::success(false, 200, 200);
            }
        }
        // Article Index
        $article->update([
            'viewers' => $article->viewers + 1
        ]);
        return ResponseFormatter::success($article, 200, 200);
    }

    public function update(Request $request, $url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $type = request()->type;
        if ($type == 'like') {
            $like = Like::where('user_id', $this->user->id)->where('article_id', $article->id)->first();
            if (is_null($like)) {
                Like::create([
                    'user_id' => $this->user->id,
                    'article_id' => $article->id
                ]);
                return ResponseFormatter::success('Favorite Added', 200, 200);
            } else {
                $like->delete();
                return ResponseFormatter::success('Favorite Removed', 200, 200);
            }
        }
    }

    public function destroy($url)
    {
        
    }

    /**Categories Function */
    protected function categories()
    {
        $categories = Category::all();
        return ResponseFormatter::success($categories);
    }
}
