<?php

namespace Metrix\LaravelPermissions\Database\Factories;

use Metrix\LaravelPermissions\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class PermissionFactory
 * @package Database\Factories
 */
class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'area' => Str::snake($this->faker->words(2, true)),
            'description' => $this->faker->text(20),
        ];
    }
}
