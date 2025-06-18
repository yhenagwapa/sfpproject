<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use App\Models\Implementation;
use App\Models\NutritionalStatus;
use App\Models\cgs_wfa_girls;
use App\Models\cgs_wfa_boys;
use App\Models\cgs_hfa_girls;
use App\Models\cgs_hfa_boys;
use App\Models\cgs_wfh_girls;
use App\Models\cgs_wfh_boys;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NutritionalStatus>
 */
class NutritionalStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function withChild(Child $child): static
    {

        return $this->state(function (array $attributes) use ($child) {

            $implementation = Implementation::where('status', 'active')->where('type', 'regular')->first();

            $weighing_date = fake()->dateTimeBetween('2025-06-01', '2025-07-30');
            $ageInMonths = $child->date_of_birth->diffInMonths($weighing_date);
            $ageInYears = floor($ageInMonths / 12);

            if ($child->sex_id === 1) {
                $weight = fake()->randomFloat(1, 8.8, 26.6);
                $height = fake()->randomFloat(1, 78.5, 125.3);
            } else {
                $weight = fake()->randomFloat(1, 8.2, 27.6);
                $height = fake()->randomFloat(1, 76.7, 124.9);
            }

            if ($child->sex_id == '1') {
                $getAge = cgs_wfa_boys::where('age_month', $ageInMonths)->first();

                if ((float) $weight <= (float) $getAge->severely_underweight) {
                    $weightForAge = 'Severely Underweight';
                } elseif ((float) $weight >= (float) $getAge->underweight_from && (float) $weight <= (float) $getAge->underweight_to) {
                    $weightForAge = 'Underweight';
                } elseif ((float) $weight >= (float) $getAge->normal_from && (float) $weight <= (float) $getAge->normal_to) {
                    $weightForAge = 'Normal';
                } elseif ((float) $weight >= (float) $weight) {
                    $weightForAge = 'Overweight';
                } else {
                    $weightForAge = 'Not Applicable';
                }

            } else {
                $getAge = cgs_wfa_girls::where('age_month', $ageInMonths)->first();

                if ((float) $weight <= (float) $getAge->severely_underweight) {
                    $weightForAge = 'Severely Underweight';
                } elseif ((float) $weight >= (float) $getAge->underweight_from && (float) $weight <= (float) $getAge->underweight_to) {
                    $weightForAge = 'Underweight';
                } elseif ((float) $weight >= (float) $getAge->normal_from && (float) $weight <= (float) $getAge->normal_to) {
                    $weightForAge = 'Normal';
                } elseif ((float) $weight >= (float) $weight) {
                    $weightForAge = 'Overweight';
                } else {
                    $weightForAge = 'Not Applicable';
                }
            }

            //height for age
            if ($child->sex_id == '1') {
                $getAge = cgs_hfa_boys::where('age_month', $ageInMonths)->first();

                if ((float) $height <= (float) $getAge->severely_stunted) {
                    $heightForAge = 'Severely Stunted';
                } elseif ((float) $height >= (float) $getAge->stunted_from && (float) $height <= (float) $getAge->stunted_to) {
                    $heightForAge = 'Stunted';
                } elseif ((float) $height >= (float) $getAge->normal_from && (float) $height <= (float) $getAge->normal_to) {
                    $heightForAge = 'Normal';
                } elseif ((float) $height >= (float) $getAge->tall) {
                    $heightForAge = 'Tall';
                } else {
                    $heightForAge = 'Not Applicable';
                }

            } else {
                $getAge = cgs_hfa_girls::where('age_month', $ageInMonths)->first();

                if ((float) $height <= (float) $getAge->severely_stunted) {
                    $heightForAge = 'Severely Stunted';
                } elseif ((float) $height >= (float) $getAge->stunted_from && (float) $height <= (float) $getAge->stunted_to) {
                    $heightForAge = 'Stunted';
                } elseif ((float) $height >= (float) $getAge->normal_from && (float) $height <= (float) $getAge->normal_to) {
                    $heightForAge = 'Normal';
                } elseif ((float) $height >= (float) $getAge->tall) {
                    $heightForAge = 'Tall';
                } else {
                    $heightForAge = 'Not Applicable';
                }
            }

            //weight for height
            if ($child->sex_id == '1') {
                $getHeight = cgs_wfh_boys::where('length_from', '<=', $height)
                          ->where('length_to', '>=', $height)
                          ->first();
                if ($getHeight) {

                    if ((float) $weight <= (float) $getHeight->severely_wasted) {
                        $weightForHeight = 'Severely Wasted';
                    } elseif ((float) $weight >= (float) $getHeight->wasted_from && (float) $weight <= (float) $getHeight->wasted_to) {
                        $weightForHeight = 'Wasted';
                    } elseif ((float) $weight >= (float) $getHeight->normal_from && (float) $weight <= (float) $getHeight->normal_to) {
                        $weightForHeight = 'Normal';
                    } elseif ((float) $weight >= (float) $getHeight->overweight_from && (float) $weight <= $getHeight->overweight_to) {
                        $weightForHeight = 'Overweight';
                    } elseif ((float) $weight >= (float) $getHeight->obese) {
                        $weightForHeight = 'Obese';
                    }

                } else {
                    $weightForHeight = 'Not Applicable';
                }

            } else {
                $getHeight = cgs_wfh_girls::where('length_from', '<=', $height)
                          ->where('length_to', '>=', $height)
                          ->first();

                if ($getHeight) {
                    if ((float) $weight <= (float) $getHeight->severely_wasted) {
                        $weightForHeight = 'Severely Wasted';
                    } elseif ((float) $weight >= (float) $getHeight->wasted_from && (float) $weight <= (float) $getHeight->wasted_to) {
                        $weightForHeight = 'Wasted';
                    } elseif ((float) $weight >= (float) $getHeight->normal_from && (float) $weight <= (float) $getHeight->normal_to) {
                        $weightForHeight = 'Normal';
                    } elseif ((float) $weight >= (float) $getHeight->overweight_from && (float) (float) $weight <= $getHeight->overweight_to) {
                        $weightForHeight = 'Overweight';
                    } elseif ((float) $weight >= (float) $getHeight->obese) {
                        $weightForHeight = 'Obese';
                    }

                } else {
                    $weightForHeight = 'Not Applicable';
                }

            }

            $isMalnourished = in_array($weightForAge, ['Underweight', 'Severely Underweight', 'Overweight']) ||
                in_array($heightForAge, ['Stunted', 'Severely Stunted']) ||
                in_array($weightForHeight, ['Wasted', 'Severely Wasted', 'Overweight', 'Obese']);


            $isUndernourished = in_array($weightForAge, ['Underweight', 'Severely Underweight']) ||
                in_array($heightForAge, ['Stunted', 'Severely Stunted']) ||
                in_array($weightForHeight, ['Wasted', 'Severely Wasted']);

            return [
                'implementation_id' => $implementation->id,
                'child_id' => $child->id,
                'weight' => $weight,
                'height' => $height,
                'actual_weighing_date' => $weighing_date,
                'age_in_months' => $ageInMonths,
                'age_in_years' => $ageInYears,
                'weight_for_age' => $weightForAge,
                'weight_for_height' => $weightForHeight,
                'height_for_age' => $heightForAge,
                'is_malnourish' => $isMalnourished,
                'is_undernourish' => $isUndernourished,
                'deworming_date' => fake()->dateTimeBetween($child->date_of_birth, '2025-05-31'),
                'vitamin_a_date' => fake()->dateTimeBetween($child->date_of_birth, '2025-05-31')
            ];
        });
    }

    public function definition(): array
    {
        return []; // leave empty, handled by withChild()
    }
}
