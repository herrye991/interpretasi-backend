<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Http\Resources\Articles\Index as ArticleIndex;
use App\Http\Resources\Articles\Show as ArticleShow;
use Intervention\Image\Facades\Image;

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
            'image' => 'required',
            'image.*' => 'image|mimetypes:image/jpeg,image/png,image/jpg',
            'categories' => 'required'
        ]);
        $url = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $request->title)) . '-'. uniqid();
        if ($request->hasfile('image')) {
            $filename = $url . "." . $request->image->getClientOriginalExtension();
            $request->image->move('assets/images/', $filename);
            Image::make('assets/images/'.$filename)->resize(600, 400, function ($constraint)
                {
                    $constraint->aspectRatio();
            })->save('assets/images/thumbnails/'.$filename);
        }
        $article = Article::create([
            'user_id' => $this->user->id,
            'url' => $url,
            'title' => $request->title,
            'content' => $request->content,
            'image' => $filename,
            'categories' => $request->categories
        ]);

        return new ArticleShow($article);
    }

    public function show($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $article->update([
            'viewers' => $article->viewers + 1
        ]);
        return new ArticleShow($article);
    }

    public function update(Request $request, $url)
    {
        $article = Article::where('url', $url)->where('user_id', $this->user->id)->firstOrFail();
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image.*' => 'image|mimetypes:image/jpeg,image/png,image/jpg',
            'categories' => 'required'
        ]);
        $filename = basename(substr($article->image, 0, strrpos($article->image, '.')));
        if ($request->hasfile('image')) {
            $filename = $filename . "." . $request->image->getClientOriginalExtension();
            $request->image->move('assets/images/', $filename);
            Image::make('assets/images/'.$filename)->resize(600, 400, function ($constraint)
                {
                    $constraint->aspectRatio();
            })->save('assets/images/thumbnails/'.$filename);
        }
        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $filename,
            'categories' => $request->categories
        ]);

        return new ArticleShow($article);
    }

    public function destroy($url)
    {
        $article = Article::where('url', $url)->where('user_id', $this->user->id)->firstOrFail();
        $article->delete();
        return ResponseFormatter::success('Article Deleted!', 200, 200);
    }

    /**Categories Function */
    protected function categories()
    {
        $categories = Category::all();
        return ResponseFormatter::success($categories);
    }
}
