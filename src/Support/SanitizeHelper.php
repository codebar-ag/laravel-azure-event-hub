<?php

namespace CodebarAg\LaravelEventLogs\Support;

class SanitizeHelper
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $keysToRemove
     * @return array<string, mixed>
     */
    public static function removeKeys(array $data = [], array $keysToRemove = []): array
    {
        $lookup = array_fill_keys(array_map('strtolower', $keysToRemove), true);

        return array_filter(
            $data,
            fn ($v, $k) => ! isset($lookup[strtolower($k)]),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
