<?php

namespace App\Enums;

final class TaskStatus
{
    public const TODO = 'todo';
    public const IN_PROGRESS = 'in_progress';
    public const REVIEW = 'review';
    public const PENDING_ADMIN = 'pending_admin';
    public const PENDING_ARBITRATION = 'pending_arbitration';
    public const REVISION = 'revision';
    public const DONE = 'done';
    public const CANCELLED = 'cancelled';

    public static function values(): array
    {
        return [
            self::TODO,
            self::IN_PROGRESS,
            self::REVIEW,
            self::PENDING_ADMIN,
            self::PENDING_ARBITRATION,
            self::REVISION,
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
            self::PENDING_ADMIN => 'Menunggu Approval',
            self::PENDING_ARBITRATION => 'Arbitrase',
            self::REVISION => 'Revisi',
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
            self::PENDING_ADMIN => 'bg-purple-100 text-purple-700',
            self::PENDING_ARBITRATION => 'bg-red-100 text-red-700',
            self::REVISION => 'bg-orange-100 text-orange-700',
            self::DONE => 'bg-green-100 text-green-700',
            self::CANCELLED => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
