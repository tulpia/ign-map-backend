<?php

namespace App\Http\Requests\Avis;

use App\Models\Avis;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AvisUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // PQ c'est au singulier ?????
        $avis = Avis::find($this->route('avi'))->first();

        return $avis && $this->user()->can('update', $avis);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'note' => 'required|integer|min:0|max:5',
            'description' => 'required|string|max:255'
        ];
    }
}
