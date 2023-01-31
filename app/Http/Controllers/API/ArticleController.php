<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Filesystem\Filesystem;

use App\Helpers\ResponseFormatter;

use App\Http\Resources\Articles\IndexCollection as ArticleIndex;
use App\Http\Resources\Articles\Show as ArticleShow;

use App\Models\Article;
use App\Models\Category;

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
        $find = request()->find;
        $category = request()->category;
        if ($type == 'categories') {
            return $this->categories();
        }
        
        $articles = $article->where('status', 'published')->with(['comments', 'likes']);
        if (!is_null($find)) {
            $articles = $articles->where('title', 'LIKE', "%{$find}%");
        }
        if (!is_null($category)) {
            $articles = $articles->where('categories', 'LIKE', "%{$category}%");
        }
        return new ArticleIndex($articles->orderBy('created_at', 'desc')->paginate(5));
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
            $request->image->move('assets/images/articles/temp', $filename);
            Image::make('assets/images/articles/temp/'.$filename)->resize(600, 400, function ($constraint)
                {
                    $constraint->aspectRatio();
            })->save('assets/images/articles/'.$filename);
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory('assets/images/articles/temp');
        }
        $article = Article::create([
            'user_id' => $this->user->id,
            'url' => $url,
            'title' => $request->title,
            'content' => $request->content,
            'image' => $filename,
            'categories' => $request->categories,
            'status' => 'drafted'
        ]);

        return ResponseFormatter::success($article, 200, 200);
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
        $filename = basename($article->image);
        if ($request->hasfile('image')) {
            $filename = $filename . "." . $request->image->getClientOriginalExtension();
            $request->image->move('assets/images/articles/temp/', $filename);
            Image::make('assets/images/articles/temp/'.$filename)->resize(600, 400, function ($constraint)
                {
                    $constraint->aspectRatio();
            })->save('assets/images/articles/'.$filename);
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory('assets/images/articles/temp');
        }
        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $filename,
            'categories' => $request->categories,
            'status' => 'drafted'
        ]);

        return ResponseFormatter::success($article, 200, 200);
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
