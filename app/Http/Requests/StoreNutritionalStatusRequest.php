<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Implementation;
use App\Models\Child;
use Carbon\Carbon;

class StoreNutritionalStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create-nutritional-status');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $implementationID = $this->input('implementation_id');
        $implementationYear = Implementation::findOrFail($implementationID);

        $yearFrom = $implementationYear->school_year_from;
        $yearTo = $implementationYear->school_year_to;

        $minWeighingDate = $yearFrom ? $yearFrom . '-01-01' : null;
        $maxDate = $yearTo ? $yearTo . '-12-31' : null;

        if ($this->input('form_type') === 'entry') {
            $childID = $this->input('child_id');
            $child = Child::findOrFail($childID);

            $childDOB = $child->date_of_birth;
            return [
                'child_id' => ['required', 'exists:children,id'],
                'weight' => ['required', 'numeric'],
                'height' => ['required', 'numeric'],
                'actual_weighing_date' => ['required', 'date', 'after_or_equal:'. $minWeighingDate, 'before_or_equal:'. $maxDate],
                'deworming_date' => ['required', 'date', 'after_or_equal:'. $childDOB, 'before_or_equal:'. $maxDate],
                'vitamin_a_date' => ['required', 'date', 'after_or_equal:'. $childDOB, 'before_or_equal:'. $maxDate],
            ];

        } elseif($this->input('form_type') === 'exit') {

            $childID = $this->input('exitchild_id');
            $child = Child::findOrFail($childID);

            $childDOB = $child->date_of_birth;
            $minDateForExit = Carbon::parse($this->input('entryWeighing'))->addDay()->format('Y-m-d');

            return [
                'exitchild_id' => ['required', 'exists:children,id'],
                'exitweight' => ['required', 'numeric'],
                'exitheight' => ['required', 'numeric'],
                'exitweighing_date' => ['required', 'date', 'after_or_equal:'. $minDateForExit, 'before_or_equal:'. $maxDate],
            ];
        }

        return [];
    }
    public function messages()
    {
        $implementationID = $this->input('implementation_id');
        $implementationYear = Implementation::findOrFail($implementationID);

        $yearFrom = $implementationYear->school_year_from;
        $yearTo = $implementationYear->school_year_to;

        $maxDate = $yearTo ? $yearTo . '-12-31' : null;

        if ($this->input('form_type') === 'entry') {
            $childID = $this->input('child_id');
            $child = Child::findOrFail($childID);

            $childDOB = $child->date_of_birth->format('Y-m-d');
            return [
                'weight.required' => 'Please fill in weight.',
                'weight.numeric' => 'Invalid entry.',
                'height.required' => 'Please fill in weight.',
                'height.numeric' => 'Invalid entry.',
                'actual_weighing_date.required' => 'Please fill in actual date of weighing.',
                'actual_weighing_date.after_or_equal' => 'Minimum date allowed is ' . $yearFrom . '.',
                'actual_weighing_date.before_or_equal' => 'Maximum date allowed is ' . $yearTo . '.',
                'deworming_date.required' => 'Please fill in deworming date.',
                'deworming_date.after_or_equal' => 'Deworming should be from ' . $childDOB . '.',
                'deworming_date.before_or_equal' => 'Deworming should be from ' . $childDOB . ' to ' . $maxDate . '.',
                'vitamin_a_date.required' => 'Please fill in Vitamin A supplementation date.',
                'vitamin_a_date.after_or_equal' => ' Vitamin A should be from ' . $childDOB . '.',
                'vitamin_a_date.before_or_equal' => ' Vitamin A should be from '  . $childDOB . ' to '. $maxDate . '.',
            ];
        } elseif($this->input('form_type') === 'exit') {
            $childID = $this->input('exitchild_id');
            $child = Child::findOrFail($childID);

            $childDOB = $child->date_of_birth->format('Y-m-d');
            $minDateForExit = Carbon::parse($this->input('entryWeighing'))->addDay()->format('Y-m-d');

            return [
                'exitweight.required' => 'Please fill in weight.',
                'exitweight.numeric' => 'Invalid entry',
                'exitheight.required' => 'Please fill in weight.',
                'exitheight.numeric' => 'Invalid entry',
                'exitweighing_date.required' => 'Please fill in actual date of weighing',
                'exitweighing_date.after_or_equal' => 'Minimum date allowed is ' . $minDateForExit . '.',
                'exitweighing_date.before_or_equal' => 'Maximum date allowed is ' . $maxDate . '.',
            ];
        }
        return [];

    }
}
