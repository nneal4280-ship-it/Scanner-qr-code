<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateQrTokenRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', \App\Models\QrToken::class) ?? false; }
    public function rules(): array { return ['site_id' => ['required', 'integer', Rule::exists('sites', 'id')->where('is_active', true)]]; }
}
