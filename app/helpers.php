<?php

use Carbon\Carbon;

function formatDecimal(float $decimal, int $decimals = 2): string
{
    return number_format($decimal, $decimals, ',', '.');
}

function formatInt(int $int): string
{
    return number_format($int, 0, ',', '.');
}

function formatDate(Carbon $date): string
{
    return $date->format('d.m.Y');
}

function formatDateTime(Carbon $date): string
{
    return $date->format('d.m.Y H:i');
}

function formatTime(Carbon $date): string
{
    return $date->format('H:i');
}

function formatTransChoice(string $key, float|int $count, array $replace = [], ?string $locale = null): string
{
    return trans_choice(
        $key,
        $count,
        array_merge(['count' => formatInt($count)], $replace),
        $locale
    );
}

function formatTransChoiceDecimal(string $key, float|int $count, int $decimals = 2, array $replace = [], ?string $locale = null): string
{
    return trans_choice(
        $key,
        $count,
        array_merge(['count' => formatDecimal($count, $decimals)], $replace),
        $locale
    );
}
