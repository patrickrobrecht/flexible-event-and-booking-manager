<?php

use Carbon\Carbon;

function fieldNameToArray(string $fieldName): string
{
    return str_replace(
        ['[]', '[', ']'],
        ['', '.', '',],
        $fieldName
    );
}

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

function formatTransChoice($key, $count, $replace = [], $locale = null): string
{
    return trans_choice(
        $key,
        $count,
        array_merge(['count' => formatInt($count)], $replace),
        $locale
    );
}
