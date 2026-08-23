<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigPresetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'config' => ['required', 'array'],
            'config.mode' => ['required', 'string', 'in:evaluate,optimize'],
            'config.provider' => ['required', 'string', 'max:50'],
            'config.model' => ['nullable', 'string', 'max:80'],
            'config.max_steps' => ['required', 'integer', 'min:1', 'max:50'],
            'config.target_score' => ['required', 'numeric', 'min:0', 'max:1'],
            'config.temperature' => ['required', 'numeric', 'min:0', 'max:2'],
            'is_default' => ['boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('is_default')) {
            $this->merge(['is_default' => $this->boolean('is_default')]);
        }
    }
}
