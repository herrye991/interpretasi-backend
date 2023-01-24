<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\Comment;
use App\Models\Article;

class CommentController extends Controller
{
    function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'destroy']]);
        $this->user = auth('api')->user();
    }

    public function index($url)
    {
        $article = Article::where('url', $url)->firstOrFail();
        $comments = Comment::where('article_id', $article->id)->with(['user' => function($user)
        {
            $user->select(['id', 'name', 'photo']);
        }])->get();
        return ResponseFormatter::success($comments, 200, 200);
    }

    public function store(Request $request, $url)
    {
        $article = Article::where('url', $url)->firstOrFail();
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

    public function destroy($url, $id)
    {
        $comment = Comment::where('id', $id)->where('user_id', $this->user->id)->firstOrFail();
        $comment->delete();
        return ResponseFormatter::success('Comment Removed', 200, 200);
    }
}
