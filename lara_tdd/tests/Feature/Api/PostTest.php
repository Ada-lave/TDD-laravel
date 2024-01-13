<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
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
        $response->assertJson([
            "id" => $post->id,
            "title" => $post->title,
            "description" => $post->description,
            "image_url" => $post->image_url,
        ]);     

        Storage::disk("local")->assertExists($post->image_url);
    }
}
