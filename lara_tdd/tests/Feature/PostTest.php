<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function a_post_can_be_stored(){
        $this->withoutExceptionHandling();

        $data = [
            "title" => "some title",
            "description" => "desc",
            'image' => "123"
        ];

        $response = $this->post('api/posts',$data);

        $response->assertOk();

        $this->assertDatabaseCount('posts',1);

        $post = Post::first();

        $this->assertEquals($data["title"],$post->title);
        $this->assertEquals($data["description"],$post->description);
        $this->assertEquals($data["image"],$post->image_url);
    }
}
