<?php

namespace App\Http\Requests\Absence;

use App\Enums\AbsenceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewJustificatifRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('review', $this->route('justificatif')) ?? false; }
    public function rules(): array { return ['status' => ['required', Rule::enum(AbsenceStatus::class), Rule::notIn([AbsenceStatus::Pending->value])], 'review_comment' => ['nullable', 'string', 'max:2000']]; }
}
