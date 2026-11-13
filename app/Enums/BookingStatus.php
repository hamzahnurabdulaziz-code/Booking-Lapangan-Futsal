<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Get the human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Confirmed => 'Terkonfirmasi',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Get the badge Tailwind CSS classes for UI display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Confirmed => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            self::Completed => 'bg-blue-50 text-blue-800 border-blue-200',
            self::Cancelled => 'bg-rose-50 text-rose-800 border-rose-200',
        };
    }
}
