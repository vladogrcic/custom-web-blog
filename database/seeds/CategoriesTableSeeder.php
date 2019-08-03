<?php

use Illuminate\Database\Seeder;
use App\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = new Category;

        $faker = Faker::create();
        $category->insert([
            'id' => 1,
            'slug' => 'general',
            'name' => 'General',
            'description' => 'General items found on the site.',
            'language_id'=> 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        for ($i=0; $i < 5; $i++) { 
            $title = $faker->unique()->word();
            $author = rand(1, 7);
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
            $created = $created->format('Y-m-d H:i:s');
            $published = $published->format('Y-m-d H:i:s');
            $category->insert([
                'id' => $i+2,
                'slug' => Str::slug($title, '-'),
                'name' => ucfirst($title),
                'description' => $faker->realText(100),
                'language_id'=> 1,
                'created_at' => $created,
                'updated_at' => $created
            ]);
        }
        // $category->insert([
        //     'id' => 2,
        //     'slug' => 'various',
        //     'name' => 'Various',
        //     'description' => 'Various items found on the site.',
        //     'language_id'=> 1,
        //     'created_at' => date("Y-m-d H:i:s"),
        //     'updated_at' => date("Y-m-d H:i:s")
        // ]);
    }
}
