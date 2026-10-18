<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Morilog\Jalali\Jalalian;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => $this->convertToGregorian($this->start_date),
            'end_date' => $this->convertToGregorian($this->end_date),
        ]);
    }

    /**
     * Convert Jalali date to Gregorian date.
     */
    private function convertToGregorian(?string $date): ?string
    {
        if (blank($date)) {
            return null;
        }

        try {
            return Jalalian::fromFormat(
                'Y/m/d',
                $date
            )
                ->toCarbon()
                ->format('Y-m-d');
        } catch (\Throwable $e) {
            return $date;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'required',
                'in:planning,active,completed,archived',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ];
    }
}