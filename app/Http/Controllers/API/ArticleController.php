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
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'required',
            'categories' => 'required'
        ]);
        Article::create([
            'user_id' => $this->user->id,
            'url' => strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $request->title)) . '-'. uniqid(),
            'title' => $request->title,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail,
            'categories' => $request->categories
        ]);

        return ResponseFormatter::success('Article Posted', 200, 200);
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
        $article = Article::where('url', $url)->where('user_id', $this->user->id)->firstOrFail();
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'required',
            'categories' => 'required'
        ]);
        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail,
            'categories' => $request->categories
        ]);

        return ResponseFormatter::success('Article Updated', 200, 200);
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
