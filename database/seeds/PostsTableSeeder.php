<?php

use Illuminate\Database\Seeder;
use App\Post;
use App\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = new Post;
        $faker = Faker::create();
        $categories = Category::All();
        for ($i=1; $i < 55; $i++) {
            $title = $faker->unique()->realText(20);
            $author = rand(1, 7);
            $content = '<p>'.$faker->realText(500).'</p>';
            $created = $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now', $timezone = null);
            $published = $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now', $timezone = null);
            if (property_exists($published, 'date')) {
                if (strtotime($published->date) < strtotime($created->date)) {
                    for ($k=0; $k < 100; $k++) {
                        if (strtotime($published->date) < strtotime($created->date)) {
                            $published = $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now', $timezone = null);
                        }
                    }
                }
            } else {
                $published = $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now', $timezone = null);
            }
            for ($j=0; $j < rand(5, 15); $j++) {
                $content .= '<p>'.$faker->realText(500).'</p>';
            }
            $created = $created->format('Y-m-d H:i:s');
            $published = $published->format('Y-m-d H:i:s');
            $posts->insert([
                'id' => $i,
                'author_id' => $author,
                'title' => $title,
                'slug' => Str::slug($title, '-'),
                'content' => $content,
                'status' => 1,
                'language_id'=> 1,
                'locked_by' => $author,
                'published_at' => $published,
                'locked' => $published,
                'created_at' => $created,
                'updated_at' => $created
            ]);
            // $categories = App\Category::all();
            // $last = $categories->count() - 1;
            // $last = 5;
            // $posts->categories()->attach( $categories[ rand(1, $last ) ] );
            
        }
    }
}
