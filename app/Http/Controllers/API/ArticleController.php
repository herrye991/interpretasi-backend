<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Http\Resources\Articles\Index as ArticleIndex;

class ArticleController extends Controller
{
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
        if ($type == 'comments') {
            $comments = Comment::where('article_id', $article->id)->with(['user' => function($user)
            {
                $user->select(['id', 'name', 'photo']);
            }])->select('comments.*')->get();
            return ResponseFormatter::success($comments, 200, 200);
        }
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
