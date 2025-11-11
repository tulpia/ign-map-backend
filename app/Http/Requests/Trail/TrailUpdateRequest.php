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
            'difficulty' => [Rule::enum(TrailDifficulty::class), 'sometimes'],
            'images' => 'nullable|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5000',
            'time_to_complete' => 'sometimes|numeric'
        ];
    }
}
