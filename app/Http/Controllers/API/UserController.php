<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use URL;
use File;

use App\Http\Resources\Articles\IndexCollection as ArticleIndex;
use App\Helpers\ResponseFormatter;
use App\Models\Article;

class UserController extends Controller
{
    function __construct()
    {
        if (!empty(auth('api')->user())) {
            $this->user = auth('api')->user();
        }
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
            $request->photo->move('assets/images/users/temp/', $filename);
            Image::make('assets/images/users/temp/'.$filename)->resize(256, 256, function ($constraint)
            {
                $constraint->aspectRatio();
            })->save('assets/images/users/'.$filename);
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory('assets/images/users/temp');
            if (strpos($user->photo, URL::to('/')) !== false) {
                File::delete('assets/images/users/' . basename($user->photo));
            }
            $user->update([
                'name' => $request->name,
                'photo' => URL::asset('/assets/images/users/'.$filename)
            ]);
        }
        $user->update([
            'name' => $request->name
        ]);
        return ResponseFormatter::success('Profile Updated!', 200, 200);
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
