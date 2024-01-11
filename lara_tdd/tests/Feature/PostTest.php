<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }
    /** @test */
    public function a_post_can_be_stored()
    {
        $this->withoutExceptionHandling();


        $file = File::create("image.png");

        $data = [
            "title" => "some title",
            "description" => "desc",
            'image' => $file
        ];

        

        $response = $this->post('api/posts',$data);

        $response->assertOk();

        $this->assertDatabaseCount('posts',1);

        $post = Post::first();

        $this->assertEquals($data["title"],$post->title);
        $this->assertEquals($data["description"],$post->description);
        $this->assertEquals('images/'.$file->hashName(),$post->image_url);

        Storage::disk("local")->assertExists($post->image_url);
    }

    /** @test */
    public function attr_title_is_required_for_storing_post()
    {


        $data = [
            'title' => '',
            "description" => "desc",
            "image" => ""
        ];

        $res = $this->post("/api/posts", $data);

        $res->assertRedirect();
        $res->assertInvalid("title");
    }

    /** @test */
    public function attr_file_is_file_for_storing_post()
    {
        // $this->withoutExceptionHandling();

        $data = [
            'title' => 'dd',
            "description" => "desc",
            "image" => "1"
        ];

        $res = $this->post("/api/posts", $data);

        $res->assertRedirect();
        $res->assertInvalid("image");
    }

    /** @test */
    public function a_post_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create();

        $file = File::create("image.png");

        $data = [
            'title' => 'dd',
            "description" => "desc",
            "image" => $file
        ];
        

        $response = $this->put("/api/posts/".$post->id, $data);

        $response->assertOk();

        $updatedPost = Post::first();
        
        $this->assertEquals($data["title"], $updatedPost->title);
        $this->assertEquals($data["description"], $updatedPost->description);
        $this->assertEquals('images/'.$file->hashName(), $updatedPost->image_url);



    }
    
}
