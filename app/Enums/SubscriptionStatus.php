<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trialing = 'trialing';
    case Active = 'active';
    case PastDue = 'past_due';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Trialing => 'Trial',
            self::Active => 'Active',
            self::PastDue => 'Payment due',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }

    public function tint(): string
    {
        return match ($this) {
            self::Trialing => 'sand',
            self::Active => 'good',
            self::PastDue => 'warn',
            self::Cancelled, self::Expired => 'dim',
        };
    }
}
