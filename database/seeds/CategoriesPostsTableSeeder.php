<?php

use Illuminate\Database\Seeder;
use App\Category;
use App\Post;

class CategoriesPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $num = [];
        $posts = Post::All();
        for ($i = 1; $i < count($posts); $i++) {
            DB::table('category_post')->insert([
                'id' => $i,
                'category_id' => rand(1, 6),
                'post_id' => $i
            ]);
        }
        for ($j = 1; $j < 25; $j++) {
            $r = rand(1, 6);
            $t = rand(1, count($posts));
            if(!(DB::table('category_post')->where('category_id', $r)->where('post_id', $t)->first()) && DB::table('category_post')->where('post_id', $t)->count() < 2)
                DB::table('category_post')->insert([
                    'id' => $i++,
                    'category_id' => $r,
                    'post_id' => $t
                ]);
        }
    }
}
