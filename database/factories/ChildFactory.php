<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Child>
 */
class ChildFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Child::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extNameOptions = ['Jr', 'Sr', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', null];
        $pantawid = ['rcct', 'mcct', null];
        $disabilities = array_merge(Child::disabilityOptions(), [null]);

        $psgcId = fake()->numberBetween(1, 3667);
        $user = function (array $attributes) use ($psgcId) {
            if (User::where('psgc_id', $psgcId)->doesntExist()) {
                return User::factory()->create(['psgc_id' => $psgcId])->id;
            }

            return User::where('psgc_id', $psgcId)
                        ->inRandomOrder()
                        ->first()
                        ->id;
        };

        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'middlename' => fake()->firstName(),
            'extension_name' => fake()->randomElement($extNameOptions),
            'date_of_birth' => fake()->dateTimeBetween('2020-01-01', '2023-06-30'),
            'sex_id' => fake()->numberBetween(1, 2),
            'address' => fake()->address(),
            'psgc_id' => $psgcId,
            'pantawid_details' => fake()->randomElement($pantawid),
            'person_with_disability_details' => fake()->randomElement($disabilities),
            'is_indigenous_people' => fake()->boolean(),
            'is_child_of_soloparent' => fake()->boolean(),
            'is_lactose_intolerant' => fake()->boolean(),
            'created_by_user_id' => $user
        ];
    }
}
