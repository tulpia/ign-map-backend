<?php

namespace App\Http\Requests\Trail;

use App\Enums\Trail\TrailDifficulty;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrailStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|unique:trails|max:255',
            'description' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'trace' => 'required|string',
            'distance' => 'required|numeric',
            'difficulty' => [Rule::enum(TrailDifficulty::class), 'required'],
            'denivele' => 'required|numeric',
            'time_to_complete' => 'required|numeric'
        ];
    }
}
