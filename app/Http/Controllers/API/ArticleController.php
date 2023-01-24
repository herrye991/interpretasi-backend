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
        $article->update([
            'viewers' => $article->viewers + 1
        ]);
        return ResponseFormatter::success($article, 200, 200);
    }

    public function update(Request $request, $url)
    {
        //
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
