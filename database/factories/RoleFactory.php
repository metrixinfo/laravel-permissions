<?php

namespace Metrix\LaravelPermissions\Database\Factories;

use Metrix\LaravelPermissions\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Role Factory
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

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
            'filter' => null,
        ];
    }
}
