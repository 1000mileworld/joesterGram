<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');  //force authentication on all methods below
    }

    public function index() 
    {
        $users = auth()->user()->following()->pluck('profiles.user_id');
        $posts = Post::whereIn('user_id', $users)->with('user')->latest()->paginate(5);

        //latest() is same as orderBy('created_at', 'DESC')
        //with('user') allows more efficient loading when showing recent pics; shortens number of queries
        
        return view('posts.index', compact('posts'));
        //return view('welcome');
    }

    public function create() {
        return view('posts/create');
    }

    public function store() 
    {
        $data = request()->validate([
            'caption' => 'required',
            'image' => ['required', 'image'],
        ]);

        //if using local storage, need to add '/storage/' to appropriate places in views (posts->index, show and profiles->index)

        // $imagePath = request('image')->store('/uploads', 'public'); //link uploaded image to publicly accessible storage
        // $image = Image::make(public_path("storage/$imagePath"))->fit(1200,1200); //resize image
        // $image->save();

        $path = Storage::disk('s3')->put('images/posts', request('image'));
        $imagePath = Storage::disk('s3')->url( $path );
        $image = Image::make($imagePath)->fit(1200,1200);

        //only authenticated users can create stuff
        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image'=> $imagePath,
        ]);

        //\App\Post::create($data);

        //dd(request()->all());

        //redirect after upload
        return redirect('/profile/' . auth()->user()->id);
    }

    public function show(\App\Post $post) 
    { //if parameter same name, Laravel fetches automatically
        return view('posts.show', [
            'post' => $post,
        ]); //can also replace array with compact('post') which matches $post to key of 'post'
    }
}
