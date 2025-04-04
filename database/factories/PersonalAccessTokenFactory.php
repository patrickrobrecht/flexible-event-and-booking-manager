<?php

namespace Database\Factories;

use App\Enums\Ability;
use App\Models\PersonalAccessToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonalAccessToken>
 */
class PersonalAccessTokenFactory extends Factory
{
    protected $model = PersonalAccessToken::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'token' => $this->faker->sha256(),
            'abilities' => $this->faker->randomElements(Ability::apiCases(), $this->faker->numberBetween(1, count(Ability::apiCases()))),
        ];
    }
}
