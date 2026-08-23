<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromptAbTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'version_a_id' => ['required', 'integer', 'exists:prompt_versions,id'],
            'version_b_id' => ['required', 'integer', 'exists:prompt_versions,id', 'different:version_a_id'],
            'benchmark_id' => ['required', 'integer', 'exists:benchmarks,id'],
        ];
    }
}
