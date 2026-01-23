<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return $posts;
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function destroy(Post $post)
    {
        
        $post->delete();
        
    }
    
    public function store(Request $request){
        $data = $request ->validated();
        $post = Post::create($data);
        return $post;
    }
            
}