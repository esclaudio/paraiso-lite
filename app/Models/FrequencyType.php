<?php

namespace App\Models;

abstract class FrequencyType
{
    const DAY       = 'day';
    const MONTH     = 'month';
    const YEAR      = 'year';

    public static function all(): array
    {
        return [
            self::DAY     => trans('Day'),
            self::MONTH   => trans('Month'),
            self::YEAR    => trans('Year'),
        ];
    }

    public static function keys(): array
    {
        return [
            self::DAY,
            self::MONTH,
            self::YEAR,
        ];
    }

    public static function pluralize(string $type, int $value): string
    {
        $types = [
            self::DAY     => [trans('day'), trans('days')],
            self::MONTH   => [trans('month'), trans('months')],
            self::YEAR    => [trans('year'), trans('years')],
        ];

        return $types[$type][$value > 1? 1: 0];
    }

    public static function description(string $type, int $value): string
    {
        if ($type === self::DAY && (int)$value === 1) {
            return trans('Daily');
        }

        if ($type === self::DAY && (int)$value === 7) {
            return trans('Weekly');
        }

        if ($type === self::MONTH && (int)$value === 1) {
            return trans('Monthly');
        }

        if ($type === self::MONTH && (int)$value === 3) {
            return trans('Quarterly');
        }

        if ($type === self::MONTH && (int)$value === 12) {
            return trans('Yearly');
        }

        if ($type === self::YEAR && (int)$value === 1) {
            return trans('Yearly');
        }

        return sprintf(trans('Every %1$s %2$s'), $value, self::pluralize($type, $value));
    }
}
