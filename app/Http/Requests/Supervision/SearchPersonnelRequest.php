<?php

namespace App\Http\Requests\Supervision;

use Illuminate\Foundation\Http\FormRequest;

class SearchPersonnelRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('viewAny', \App\Models\User::class) ?? false; }
    public function rules(): array { return ['q' => ['nullable', 'string', 'max:100']]; }
}
