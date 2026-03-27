<?php

namespace App\Support;

use App\Models\ActionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ActionLogger
{
    public static function log(
        string $action,
        string $description,
        ?Model $target = null,
        array $properties = [],
        ?Request $request = null,
        ?int $userId = null,
    ): void {
        if (! Schema::hasTable('action_logs')) {
            return;
        }

        ActionLog::create([
            'user_id' => $userId ?? $request?->user()?->id,
            'action' => $action,
            'target_type' => $target ? class_basename($target) : null,
            'target_id' => $target?->getKey(),
            'description' => $description,
            'ip_address' => $request?->ip(),
            'properties' => $properties ?: null,
        ]);
    }
}
