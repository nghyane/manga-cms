<?php

namespace Modules\Anime\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AnimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Anime\Entities\Anime::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'description' => $this->faker->text,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\Modules\Anime\Entities\Anime $anime) {
            $anime->tags()->attach(\Modules\Anime\Entities\Tags::factory()->create());
            $anime->genres()->attach(\Modules\Anime\Entities\Genres::factory()->create());
        });
    }
}

