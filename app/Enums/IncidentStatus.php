<?php

namespace App\Enums;

enum IncidentStatus: string
{
    case Pending = 'pending';
    case Resolved = 'resolved';
    case InProgress = 'in_progress';
    case Dismissed = 'dismissed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
