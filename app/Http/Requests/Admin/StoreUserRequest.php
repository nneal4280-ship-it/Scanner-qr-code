<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRole(UserRole::Administrateur) ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'position' => ['required', 'string', 'max:150'],
            'department' => ['required', 'string', 'max:150'],
            'role' => ['required', new Enum(UserRole::class)],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
