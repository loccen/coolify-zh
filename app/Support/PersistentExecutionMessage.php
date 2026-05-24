<?php

namespace App\Support;

class PersistentExecutionMessage
{
    public static function forDisplay(?string $message): ?string
    {
        if ($message === null) {
            return null;
        }

        return match ($message) {
            'Marked as failed during Coolify startup - job was interrupted' => __($message),
            default => $message,
        };
    }
}
