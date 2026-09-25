<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', \App\Models\Rapport::class) ?? false; }
    public function rules(): array { return ['period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after_or_equal:period_start']]; }
}
