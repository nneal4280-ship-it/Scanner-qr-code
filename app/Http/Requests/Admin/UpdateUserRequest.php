<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('update', $this->route('user')) ?? false; }
    public function rules(): array { return ['role' => ['required', Rule::enum(UserRole::class)], 'supervisor_id' => ['nullable', 'integer', 'exists:users,id']]; }
}
