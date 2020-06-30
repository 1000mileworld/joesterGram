<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;

class ProfilesController extends Controller
{
    public function index(User $user)
    {
        //$user = User::findOrFail($user);
        
        // return view('profiles.index', [
        //     'user' => $user,
        // ]);

        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

        $postCount = Cache::remember(
            'count.posts.' . $user->id, 
            now()->addSeconds(30), //store for 30s in cache
            function() use($user) {
                return $user->posts->count();
            });

        $followersCount = Cache::remember(
            'count.followers.' . $user->id,
            now()->addSeconds(30),
            function() use($user) {
                return $user->profile->followers->count();
            });

        $followingCount = Cache::remember(
            'count.following.' . $user->id,
            now()->addSeconds(30),
            function() use($user) {
                return $user->following->count();
            });

        //$postCount = $user->posts->count();
        //$followersCount = $user->profile->followers->count();
        //$followingCount = $user->following->count();


        return view('profiles.index', compact('user', 'follows', 'postCount', 'followersCount', 'followingCount'));
    }

    public function edit(User $user) //using \App\User (or User since it's been imported) is equivalent to findOrFail 
    {
        $this->authorize('update', $user->profile);

        return view('profiles.edit', compact('user'));
    }

    public function update(User $user)
    {
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
        ]);

        if(request('image')) {
            // if using local storage, need to run php artisan storage:link and edit image path in app/Profile.php to include '/storage/'

            // $imagePath = request('image')->store('profile', 'public'); //link uploaded image to publicly accessible storage
            // $image = Image::make(public_path("storage/$imagePath"))->fit(1000,1000); //resize image
            // $image->save();
            // $imageArray = ['image' => $imagePath];

            dd(Storage::disk('s3'));
            $path = Storage::disk('s3')->put('images/profile', request('image'));
            //dd($path);
            $fullPath = Storage::disk('s3')->url( $path );
            //dd($fullPath);
            
            $image = Image::make($fullPath)->fit(1000,1000);
            //$image->save();
            $imageArray = ['image' => $fullPath];;

        }
        
        auth()->user()->profile->update(array_merge(
            $data,
            $imageArray ?? [] //for the case where user don't pass in a profile image when editing
        ));
        return redirect("/profile/{$user->id}");
    }
}
