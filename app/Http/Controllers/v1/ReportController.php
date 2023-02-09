<?php

namespace App\Http\Controllers\v1;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\ReportUser;
use App\Models\ReportArticle;
use App\Models\ReportComment;
use App\Models\User;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function user ($id, Request $request) {
        $user = User::where('id', $id)->firstOrFail();
        $request->validate([
            'reason' => 'required'
        ]);
        ReportUser::create([
            'user_id' => $this->user->id,
            'reported_user_id' => $user->id,
            'reason' => $request->reason
        ]);
        return ResponseFormatter::success('User Reported!', 200, 200);
    }

    public function article ($url, Request $request) {
        $article = Article::where('url', $url)->firstOrFail();
        $request->validate([
            'reason' => 'required'
        ]);
        ReportArticle::create([
            'user_id' => $this->user->id,
            'reported_article_id' => $article->id,
            'reason' => $request->reason
        ]);
        return ResponseFormatter::success('Article Reported!', 200, 200);
    }

    public function comment ($url, $id, Request $request) {
        $comment = Comment::where('id', $id)->firstOrFail();
        $request->validate([
            'reason' => 'required'
        ]);
        ReportComment::create([
            'user_id' => $this->user->id,
            'reported_comment_id' => $comment->id,
            'reason' => $request->reason
        ]);
        return ResponseFormatter::success('Comment Reported!', 200, 200);
    }
}
