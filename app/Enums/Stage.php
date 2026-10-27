<?php

namespace App\Enums;

enum Stage: string
{
    case Pregnancy = 'pregnancy';
    case Postpartum = 'postpartum';
    case MomBaby = 'mom_baby';
    case Any = 'any';

    public function label(): string
    {
        return match ($this) {
            self::Pregnancy => 'Pregnancy',
            self::Postpartum => 'Postpartum',
            self::MomBaby => 'Mom & baby',
            self::Any => 'Any stage',
        };
    }

    /** Chip colour key used by the views. */
    public function tint(): string
    {
        return match ($this) {
            self::Pregnancy => 'slate',
            self::Postpartum => 'blush',
            self::MomBaby => 'sand',
            self::Any => 'dim',
        };
    }
}
