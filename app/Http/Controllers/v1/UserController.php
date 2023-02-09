<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use URL;
use File;

use App\Http\Resources\Articles\IndexCollection as ArticleIndex;
use App\Helpers\ResponseFormatter;
use App\Helpers\Domain;
use App\Helpers\Path;
use App\Models\Article;
Use App\Models\User;

class UserController extends Controller
{
    function __construct()
    {
        if (auth('api')->check()) {
            $this->user = auth('api')->user();
        }
    }

    public function show ($id)
    {
        $user = User::with(['articles' => function($query){
            $query->select('id', 'user_id', 'url', 'title', 'image', 'viewers')->withCount(['comments', 'likes']);
        }])->where('id', $id)->firstOrFail();
        return ResponseFormatter::success($user, 200, 200);
    }
    
    public function getProfile()
    {
        return ResponseFormatter::success($this->user, 200, 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $this->user;
        $request->validate([
            'name' => 'required|min:2',
            'photo.*' => 'image|mimetypes:image/jpeg,image/png,image/jpg'
        ]);
        if ($request->hasfile('photo')) {
            $filename = uniqid() . "." . $request->photo->getClientOriginalExtension();
            $request->photo->move(Path::public('assets/images/users/temp/'), $filename);
            Image::make(Path::public('assets/images/users/temp/'.$filename))->resize(256, 256, function ($constraint)
            {
                $constraint->aspectRatio();
            })->save(Path::public('assets/images/users/'.$filename));
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory(Path::public('assets/images/users/temp'));
            if (strpos($user->photo, URL::to('/')) !== false) {
                File::delete(Path::public('assets/images/users/' . basename($user->photo)));
            }
            $user->update([
                'name' => $request->name,
                'photo' => Domain::base('assets/images/users/'.$filename)
            ]);
        }
        $user->update([
            'name' => $request->name
        ]);
        return ResponseFormatter::success('Profile Updated!', 200, 200);
    }

    public function articles(Article $article)
    {
        $status = request()->status;
        $articles = $article->with(['comments', 'likes'])->where('user_id', $this->user->id);
        return new ArticleIndex($articles->orderBy('created_at', 'desc')->paginate(5));
    }

    public function articlesType($type)
    {
        $articles = Article::with(['comments', 'likes']);
        if ($type == 'history') {
            $articles = $articles->join('histories', 'histories.article_id', 'articles.id')->where('histories.user_id', $this->user->id)->select('articles.*')->orderBy('articles.updated_at', 'desc');
        } elseif ($type == 'drafted') {
            $articles = $articles->where('user_id', $this->user->id)->where('status', 'drafted')->orderBy('created_at', 'desc');
        } elseif ($type == 'moderated') {
            $articles = $articles->where('user_id', $this->user->id)->where('status', 'moderated')->orderBy('created_at', 'desc');
        } elseif ($type == 'published') {
            $articles = $articles->where('user_id', $this->user->id)->where('status', 'published')->orderBy('created_at', 'desc');
        } elseif ($type == 'rejected') {
            $articles = $articles->where('user_id', $this->user->id)->where('status', 'rejected')->orderBy('created_at', 'desc');
        } elseif ($type == 'banned') {
            $articles = $articles->where('user_id', $this->user->id)->where('status', 'banned')->orderBy('created_at', 'desc');
        } else {
            abort(404);
        }
        return new ArticleIndex($articles->paginate(5));
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);
        $user = $this->user;
        if ($user->set_password == '0') {
            $user->update([
                'password' => Hash::make($request->password),
                'set_password' => '1'
            ]);
            return ResponseFormatter::success('Password Added!', 200, 200);
        }
        return ResponseFormatter::error('Password already added!', 403, 403);
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
