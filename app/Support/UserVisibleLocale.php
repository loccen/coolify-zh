<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class UserVisibleLocale
{
    public static function resolve(?string $locale = null): string
    {
        $normalized = static::normalize($locale);
        if ($normalized !== null) {
            return $normalized;
        }

        if (static::hasRequestContext()) {
            return static::normalize(App::currentLocale()) ?? static::fallback();
        }

        return static::fallback();
    }

    public static function fallback(): string
    {
        return static::normalize(config('app.fallback_locale', 'en')) ?? 'en';
    }

    public static function withLocale(?string $locale, callable $callback): mixed
    {
        $previousLocale = App::currentLocale();

        App::setLocale(static::resolve($locale));

        try {
            return $callback();
        } finally {
            App::setLocale($previousLocale);
        }
    }

    public static function normalize(?string $locale): ?string
    {
        if (! is_string($locale)) {
            return null;
        }

        $normalized = str_replace('-', '_', trim($locale));
        if ($normalized === '') {
            return null;
        }

        $language = strtolower(explode('_', $normalized)[0]);

        return match (true) {
            $language === 'en' => 'en',
            $language === 'zh' => 'zh_CN',
            default => null,
        };
    }

    private static function hasRequestContext(): bool
    {
        if (! app()->bound('request')) {
            return false;
        }

        $request = request();

        return $request instanceof Request && $request->route() !== null;
    }
}
