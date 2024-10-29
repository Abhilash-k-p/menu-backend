<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{

    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parent = Menu::inRandomOrder()->first();

        return [
            'id' => (string) Str::uuid(),  // Generate UUID for the 'id' field
            'name' => $this->faker->domainName,
            'parent_id' => $parent?->id,  // Random parent menu or null
            'depth' => $parent ? $this->calculateDepth($parent) : 0,  // Calculate depth
        ];
    }

    private function calculateDepth($parent): int
    {
        $depth = 0;
        while ($parent) {
            $parent = Menu::find($parent->parent_id);
            $depth++;
        }
        return $depth;
    }
}
