<?php

namespace App\Models;

abstract class DocumentStatus
{
    const DRAFT      = 'draft';
    const TO_REVIEW  = 'toreview';
    const TO_APPROVE = 'toapprove';
    const PUBLISHED  = 'published';
    const ARCHIVED   = 'archived';
    const REJECTED   = 'rejected';

    public static function all(): array
    {
        return [
            self::DRAFT      => trans('Draft'),
            self::TO_REVIEW  => trans('To Review'),
            self::TO_APPROVE => trans('To Approve'),
            self::PUBLISHED  => trans('Published'),
            self::ARCHIVED   => trans('Archived'),
            self::REJECTED   => trans('Rejected'),
        ];
    }

    public static function keys(): array
    {
        return [
            self::DRAFT,
            self::TO_REVIEW,
            self::TO_APPROVE,
            self::PUBLISHED,
            self::ARCHIVED,
            self::REJECTED,
        ];
    }

    public static function description(string $status): string
    {
        return self::all()[$status] ?? $status;
    }

    public static function html(string $status): string
    {
        $html = [
            self::DRAFT      => '<span class="badge badge-info">%s</span>',
            self::TO_REVIEW  => '<span class="badge badge-warning">%s</span>',
            self::TO_APPROVE => '<span class="badge badge-warning">%s</span>',
            self::PUBLISHED  => '<span class="badge badge-success">%s</span>',
            self::ARCHIVED   => '<span class="badge badge-secondary">%s</span>',
            self::REJECTED   => '<span class="badge badge-danger">%s</span>',
        ];

        return sprintf($html[$status] ?? '%s', self::description($status));
    }
}
