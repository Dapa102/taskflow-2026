<?php

namespace App\Enums;

final class TaskStatus
{
    public const TODO = 'todo';
    public const IN_PROGRESS = 'in_progress';
    public const REVIEW = 'review';
    public const DONE = 'done';
    public const CANCELLED = 'cancelled';

    public static function values(): array
    {
        return [
            self::TODO,
            self::IN_PROGRESS,
            self::REVIEW,
            self::DONE,
            self::CANCELLED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::REVIEW => 'Review',
            self::DONE => 'Done',
            self::CANCELLED => 'Cancelled',
        ];
    }

    public static function label(?string $status): string
    {
        return self::labels()[$status] ?? ucfirst((string) $status);
    }

    public static function badgeClass(?string $status): string
    {
        return match ($status) {
            self::TODO => 'bg-gray-100 text-gray-700',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-700',
            self::REVIEW => 'bg-yellow-100 text-yellow-700',
            self::DONE => 'bg-green-100 text-green-700',
            self::CANCELLED => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
