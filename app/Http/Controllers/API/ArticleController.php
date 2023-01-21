<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Http\Resources\Articles\Index as ArticleIndex;
use App\Models\Like;

class ArticleController extends Controller
{
    function __construct()
    {
        $this->user = auth('api')->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Article $article)
    {
        $type = request()->type;
        if ($type == 'category') {
            $categories = Category::all();
            return ResponseFormatter::success($categories);
        }
        $articles = $article->with('comments')->get();
        return ArticleIndex::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $type = request()->type;
        // Comments Index
        if ($type == 'comments') {
            $comments = Comment::where('article_id', $article->id)->with(['user' => function($user)
            {
                $user->select(['id', 'name', 'photo']);
            }])->get();
            return ResponseFormatter::success($comments, 200, 200);
        }
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $type = request()->type;
        if (!is_null($this->user)) {
            if ($type == 'comments') {
                $request->validate([
                    'body' => 'required|min:1|max:255'
                ]);
                Comment::create([
                    'user_id' => $this->user->id,
                    'article_id' => $article->id,
                    'body' => $request->body
                ]);
                return ResponseFormatter::success('Comment Added', 200, 200);
            }
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
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($url)
    {
        $id = request()->id;
        $type = request()->type;
        if ($type == 'comments') {
            if (!is_null($this->user)) {
                $comment = Comment::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();
                $comment->delete();
                return ResponseFormatter::success('Comment Removed', 200, 200);
            }
        }
    }
}
