<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function delete($id)
    {
        $post = Post::find($id);
        $post->delete();
    }
    public function show($id)
    {
        $post = Post::find($id);

        return view("posts.show", compact("post"));
    }
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact("posts"));
    }
    public function store(StoreRequest $request){
        $data = $request->validated();

        if(isset($data['image']) && !empty($data['image'])){
            $path = Storage::disk("local")->put('/images', $data['image']);
            $data['image_url'] = $path;
        }

        unset($data['image']);
        $post = Post::create($data);

        return  PostResource::make($post)->resolve();

        
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $post = Post::find($id);

        if(isset($data['image']) && !empty($data['image'])){
            $path = Storage::disk("local")->put('/images', $data['image']);
            $data['image_url'] = $path;
        }

        unset($data['image']);
        
        $post->update($data);
    }
    
}
