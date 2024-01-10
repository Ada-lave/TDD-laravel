<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

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
            'img' => "123"
        ];

        $response = $this->post('api/posts',$data);

        $response->assertOk();

        $this->assertDatabaseCount('posts',1);
    }
}
