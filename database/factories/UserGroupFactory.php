<?php

namespace Metrix\LaravelPermissions\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Metrix\LaravelPermissions\Models\UserGroup;

/**
 * User group factory
 */
class UserGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'id' => null,
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->text(20),
        ];
    }
}
