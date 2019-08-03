<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class CategoryTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
    // public function testCreateCategoryWithMiddleware()
    // {
    //     $data = [
    //             // '_token' => "",
    //             // 'id' => "New Product",
    //             'name' => "New Category",
    //             'desc' => "This is a category",
    //             'slug' => 'new-category',
    //             'language_id' => 1,
    //             'orderBy' => 'id',
    //             'orderDir' => 'desc',
    //                     ];
    //     $response = $this->json('POST', url('/').'/manage/categories/create',$data);
    //     $response->assertStatus(401);
    //     $response->assertJson(['message' => "Unauthenticated."]);
    // }
    public function testCreateCategory()
    {
        $this->withoutMiddleware();
        $data = [
                '_token' => csrf_token(),
                'id' => 15,
                'name' => "New Category",
                'desc' => "This is a category",
                'slug' => 'new-category',
                'language_id' => 1,
                'orderBy' => 'id',
                'orderDir' => 'desc',
                        ];
                        // $permission   = factory(\App\Permission::class)->create();
                        $permission   = \App\Permission::where('name', 'create-general')->first();
                        $user   = factory(\App\User::class)->create();
                        $user->permissions()->sync($permission);
                        // $user   = \App\User::find(1);
                        $response = $this->actingAs($user, 'web')->json('POST', url('/').'/manage/categories',$data);
                        $this->assertEquals(200, $response->status());
                        

                        // $response->assertStatus(200);
                        // $response->assertJson(['statusText'        => 'OK']);
                        // $response->assertJson(['message'       => "Category Created!"]);
                        // $response->assertJsonStructure([
                        //     'data' => [
                        //         'items',
                        //         'maxCol',
                        //         'pageCount'
                        //     ]
                        // ]);
    }
}
