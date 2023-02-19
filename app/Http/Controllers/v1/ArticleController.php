<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Filesystem\Filesystem;
use File;
use Carbon\Carbon;

use App\Helpers\ResponseFormatter;
use App\Helpers\Domain;
use App\Helpers\Path;

use App\Http\Resources\Articles\IndexCollection as ArticleIndex;
use App\Http\Resources\Articles\Show as ArticleShow;

use App\Models\Article;
use App\Models\Category;
use App\Models\History;

class ArticleController extends Controller
{
    function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy', 'uploadImage', 'history']]);
        if (auth('api')->check()) {
            $this->user = auth('api')->user();
        }
    }
    
    public function index()
    {
        $articles = Article::query(); //where('status', 'published');
        $type = request()->type;
        $find = request()->find;
        $category = request()->category;
        $trending = request()->trending;
        $take = request()->take;
        $skip = request()->skip;
        $orderBy = request()->orderBy;
        if ($type == 'categories') {
            return $this->categories();
        }
        $articles = $articles->with(['comments', 'likes', 'user']);
        if (!is_null($find)) {
            $articles = $articles->where('title', 'LIKE', "%{$find}%");
        }
        if (!is_null($category)) {
            $articles = $articles->where('category_id', $category);
        }
        if (!is_null($trending)) {
            if ($trending == true) {
                $articles = $articles->where('trending', '1')->orderBy('viewers', 'desc');
            } elseif ($trending == false) {
                $articles = $articles->where('trending', '0')->orderBy('viewers', 'desc');
            }
        }
        if (!is_null($orderBy)) {
            if ($orderBy == 'lastest') {
                $articles = $articles->orderBy('created_at', 'desc');
            }
            if ($orderBy == 'mostView') {
                $articles = $articles->orderBy('viewers', 'desc');
            }
        }
        if (!is_null($take) && !is_null($skip)) {
            $articles = $articles->skip($skip)->take($take)->get();
        } else {
            $articles = $articles->paginate(5);
        }
        return new ArticleIndex($articles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'original_content' => 'required',
            'image' => 'required',
            'image.*' => 'image|mimetypes:image/jpeg,image/png,image/jpg',
            'tags' => 'required'
        ]);
        $url = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $request->title)) . '-'. uniqid();
        if ($request->hasfile('image')) {
            $filename = $url . "." . $request->image->getClientOriginalExtension();
            $request->image->move(Path::public('assets/images/articles/temp'), $filename);
            Image::make(Path::public('assets/images/articles/temp/'.$filename))->resize(600, 400, function ($constraint)
                {
                    $constraint->aspectRatio();
            })->save(Path::public('assets/images/articles/'.$filename));
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory(Path::public('assets/images/articles/temp'));
        }
        $article = Article::create([
            'user_id' => $this->user->id,
            'category_id' => $request->category_id,
            'url' => $url,
            'title' => $request->title,
            'content' => $request->content,
            'original_content' => $request->original_content,
            'image' => Domain::base('assets/images/articles/'. $filename),
            'status' => 'drafted',
            'tags' => $request->tags
        ]);

        return ResponseFormatter::success($article, 200, 200);
    }

    public function show($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        if (auth('api')->check()) {
            $this->history($article);
        }
        return new ArticleShow($article);
    }

    public function update(Request $request, $url)
    {
        $article = Article::where('url', $url)->where('user_id', $this->user->id)->firstOrFail();
        
        $status = request()->status;
        if (!is_null($status)) {
            if ($status == 'moderated') {
                $article->update([
                    'status' => 'moderated'
                ]);
                return ResponseFormatter::success('Status updated to moderated!', 200, 200);
            }
            return ResponseFormatter::error('Only can update status to moderated!', 400, 400);
        }
        
        $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'original_content' => 'required',
            'image.*' => 'image|mimetypes:image/jpeg,image/png,image/jpg',
            'tags' => 'required'
        ]);
        $filename = basename($article->image);
        if ($request->hasfile('image')) {
            $filename = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $request->title)) . '-'. uniqid() . "." . $request->image->getClientOriginalExtension();
            $request->image->move(Path::public('assets/images/articles/temp/'), $filename);
            Image::make(Path::public('assets/images/articles/temp/'.$filename))->resize(600, 400, function ($constraint)
                {
                    $constraint->aspectRatio();
            })->save(Path::public('assets/images/articles/'.$filename));
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory(Path::public('assets/images/articles/temp'));
            File::delete(Path::public('assets/images/articles/'.basename($article->image)));
        }
        $article->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'content' => $request->content,
            'original_content' => $request->original_content,
            'image' => Domain::base('assets/images/articles/'. $filename),
            'status' => 'drafted',
            'tags' => $request->tags
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

    public function history($article)
    {
        $history = History::where('user_id', $this->user->id)->where('article_id', $article->id)->first();
        if ($article->user_id !== $this->user->id) {
            if (empty($history)) {
                History::create([
                    'user_id' => $this->user->id,
                    'article_id' => $article->id
                ]);
            } else {
                $history->update([
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }

    public function tag ($tag) {
        $articles = Article::whereJsonContains('tags', $tag)->paginate(5);
        return new ArticleIndex($articles);
    }

    public function uploadImage (Request $request)
    {
        $request->validate([
            'image' => 'required',
            'image.*' => 'image|mimetypes:image/jpeg,image/png,image/jpg',
        ]);
        if ($request->hasfile('image')) {
            $filename = uniqid() . "." . $request->image->getClientOriginalExtension();
            $request->image->move(Path::public('assets/images/articles/contents/temp'), $filename);
            Image::make(Path::public('assets/images/articles/contents/temp/'.$filename))->resize(600, 400, function ($constraint)
                {
                    $constraint->aspectRatio();
            })->save(Path::public('assets/images/articles/contents/'.$filename));
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory(Path::public('assets/images/articles/contents/temp'));
        }

        return response()->json(['url' => Domain::base('assets/images/articles/contents/'. $filename)]);
    }

    public function preview($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $article->update([
            'viewers' => $article->viewers + 1
        ]);
        return ResponseFormatter::success('Success', 200, 200);
    }
}
