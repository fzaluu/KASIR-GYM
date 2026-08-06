<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log(string $aksi, string $deskripsi, ?int $userId = null): void
    {
        ActivityLog::create([
            'user_id'   => $userId ?? auth()->id(),
            'aksi'      => $aksi,
            'deskripsi' => $deskripsi,
        ]);
    }
}