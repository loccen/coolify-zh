<?php

namespace App\Notifications;

use App\Support\UserVisibleLocale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $backoff = [10, 20, 30, 40, 50];

    public $tries = 5;

    public $maxExceptions = 5;

    protected function useLocale(?string $locale = null): void
    {
        $this->locale = UserVisibleLocale::resolve($locale);
    }

    protected function userVisibleLocale(): string
    {
        return UserVisibleLocale::resolve($this->locale ?? null);
    }

    protected function trans(string $key, array $replace = []): string
    {
        return Lang::get($key, $replace, $this->userVisibleLocale());
    }

    protected function withUserVisibleLocale(callable $callback): mixed
    {
        return UserVisibleLocale::withLocale($this->userVisibleLocale(), $callback);
    }
}
