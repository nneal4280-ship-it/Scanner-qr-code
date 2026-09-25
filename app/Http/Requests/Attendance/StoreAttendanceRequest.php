<?php

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', \App\Models\Pointage::class) ?? false; }
    public function rules(): array
    {
        return [
            'site_id' => ['required', 'integer', Rule::exists('sites', 'id')->where('is_active', true)],
            'qr_token' => ['required', 'string', 'min:1', 'max:255'],
            'type' => ['required', Rule::enum(AttendanceType::class)],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:10000'],
        ];
    }
}
