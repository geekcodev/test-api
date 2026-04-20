<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::New => in_array($newStatus, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($newStatus, [self::Processing, self::Cancelled]),
            self::Processing => $newStatus === self::Shipped,
            self::Shipped => $newStatus === self::Completed,
            self::Completed, self::Cancelled => false,
        };
    }
}
