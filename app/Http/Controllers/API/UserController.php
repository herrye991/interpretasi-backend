<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\Articles\IndexCollection as ArticleIndex;

class UserController extends Controller
{
    function __construct()
    {
        $this->user = auth('api')->user();
    }
    
    public function index()
    {
        return $this->user;
    }

    public function myArticles(Article $article)
    {
        $status = request()->status;
        $articles = $article->with(['comments', 'likes'])->where('user_id', $this->user->id);
        if ($status == 'drafted') {
            $articles->where('status', 'drafted');
        }
        if ($status == 'moderated') {
            $articles->where('status', 'moderated');
        }
        if ($status == 'published') {
            $articles->where('status', 'published');
        }
        if ($status == 'rejected') {
            $articles->where('status', 'rejected');
        }
        if ($status == 'banned') {
            $articles->where('status', 'banned');
        }
        return new ArticleIndex($articles->paginate(5));
    }
}
