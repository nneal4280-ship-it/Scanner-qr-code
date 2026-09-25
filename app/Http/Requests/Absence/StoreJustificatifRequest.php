<?php

namespace App\Http\Requests\Absence;

use Illuminate\Foundation\Http\FormRequest;

class StoreJustificatifRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', \App\Models\Justificatif::class) ?? false; }
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:2000'],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
