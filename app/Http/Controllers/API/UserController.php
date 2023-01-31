<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
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
            $articles = $articles->where('status', 'drafted');
        }
        if ($status == 'moderated') {
            $articles = $articles->where('status', 'moderated');
        }
        if ($status == 'published') {
            $articles = $articles->where('status', 'published');
        }
        if ($status == 'rejected') {
            $articles = $articles->where('status', 'rejected');
        }
        if ($status == 'banned') {
            $articles = $articles->where('status', 'banned');
        }
        return new ArticleIndex($articles->orderBy('created_at', 'desc')->paginate(5));
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);
        $user = $this->user;
        if ($user->set_password == '0') {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            return ResponseFormatter::success('Password Added!', 200, 200);
        }
        return ResponseFormatter::error('Password already added!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);
        $user = $this->user;
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            return ResponseFormatter::success('Password changed!', 200, 200);
        }
        return ResponseFormatter::error('Old password is wrong!', 403, 403);
    }

    public function check()
    {
        if ($this->user->set_password == '0') {
            $set_password = false;
        } else {
            $set_password = true;
        }
        if (is_null($this->user->email_verified_at)) {
            $email_verified = false;
        } else {
            $email_verified = true;
        }
        $response = [
            'set_password' => $set_password,
            'email_verified' => $email_verified
        ];
        return response()->json($response);
    }
}
