<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromptRegressionTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'benchmark_ids' => ['nullable', 'array'],
            'benchmark_ids.*' => ['integer', 'exists:benchmarks,id'],
            'sample_input' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
