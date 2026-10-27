<?php

namespace App\Enums;

enum PlanInterval: string
{
    case Monthly = 'monthly';
    case Yearly = 'yearly';
    case Once = 'once';

    public function suffix(): string
    {
        return match ($this) {
            self::Monthly => '/month',
            self::Yearly => '/year',
            self::Once => ' once-off',
        };
    }

    public function periodDays(): ?int
    {
        return match ($this) {
            self::Monthly => 31,
            self::Yearly => 366,
            self::Once => null,
        };
    }
}
