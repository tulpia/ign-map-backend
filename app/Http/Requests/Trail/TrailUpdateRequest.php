<?php

namespace App\Http\Requests\Trail;

use App\Enums\Trail\TrailDifficulty;
use App\Models\Trail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrailUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $trail = Trail::find($this->route('trail'));

        return $trail && $this->user()->can('update', $trail);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'sometimes',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'trace' => 'sometimes|string',
            'distance' => 'sometimes|numeric',
            'difficulty' => [Rule::enum(TrailDifficulty::class), 'sometimes'],
            'denivele' => 'sometimes|numeric',
            'time_to_complete' => 'sometimes|numeric'
        ];
    }
}
