<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

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

    /** @test */
    public function response_for_route_posts_index_is_view_post_index_with_posts()
    {
        $this->withoutExceptionHandling();

        $posts = Post::factory(10)->create();

        $res = $this->get("/posts");

        $res->assertViewIs("posts.index");

        $res->assertSeeText("View PAGE");

        $titles = $posts->pluck('title')->toArray();

        $res->assertSeeText($titles);
    }

    /** @test */
    public function response_for_route_posts_can_be_show_single_post()
    {
        $this->withoutExceptionHandling();

        $posts = Post::factory(10)->create();

        $post = Post::find(1);

        $res = $this->get("/posts/".$post->id);

        $res->assertOk();
        $res->assertViewIs("posts.show");
        $res->assertSeeText($post->title);
    }

    /** @test */
    public function a_post_can_be_del_by_auth_user()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $post = Post::factory()->create();

        $res = $this->actingAs($user)->delete('/api/posts/' . $post->id);
        $res->assertOk();
        $this->assertDatabaseCount('posts',0);

    }
}
