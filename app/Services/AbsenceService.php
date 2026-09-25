<?php

namespace App\Services;

use App\Enums\AbsenceStatus;
use App\Models\Justificatif;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AbsenceService
{
    public function submit(User $user, array $data, ?UploadedFile $file = null): Justificatif
    {
        return DB::transaction(function () use ($user, $data, $file): Justificatif {
            $fileData = [];
            if ($file) {
                $fileData = [
                    'file_path' => $file->store('justificatifs', 'private'),
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ];
            }

            return Justificatif::create(array_merge($data, $fileData, [
                'user_id' => $user->id,
                'status' => AbsenceStatus::Pending,
                'submitted_at' => now(),
            ]));
        });
    }

    public function review(Justificatif $justificatif, User $reviewer, AbsenceStatus $status, ?string $comment): Justificatif
    {
        $justificatif->update(['reviewer_id' => $reviewer->id, 'status' => $status, 'reviewed_at' => now(), 'review_comment' => $comment]);
        return $justificatif->refresh();
    }
}
