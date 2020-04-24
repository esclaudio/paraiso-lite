<?php

namespace App\Models;

abstract class DocumentStatus
{
    const DRAFT      = 'draft';
    const TO_REVIEW  = 'to_review';
    const TO_APPROVE = 'to_approve';
    const PUBLISHED  = 'published';
    const ARCHIVED   = 'archived';
    const REJECTED   = 'rejected';

    public static function all(): array
    {
        return [
            self::DRAFT      => 'draft',
            self::TO_REVIEW  => 'to_review',
            self::TO_APPROVE => 'to_approve',
            self::PUBLISHED  => 'published',
            self::ARCHIVED   => 'archived',
            self::REJECTED   => 'rejected',
        ];
    }

    public static function description(string $key)
    {
        switch($key) {
            case self::DRAFT:
                return MSG_WORKFLOW_DRAFT;
            case self::TO_REVIEW:
                return MSG_WORKFLOW_TO_REVIEW;
            case self::TO_APPROVE:
                return MSG_WORKFLOW_TO_APPROVE;
            case self::PUBLISHED:
                return MSG_WORKFLOW_PUBLISHED;
            case self::ARCHIVED:
                return MSG_WORKFLOW_ARCHIVED;
            case self::REJECTED:
                return MSG_WORKFLOW_REJECTED;
        }

        return null;
    }

    public static function colors()
    {
        return [
            self::DRAFT      => 'secondary',
            self::TO_REVIEW  => 'warning',
            self::TO_APPROVE => 'warning',
            self::PUBLISHED  => 'success',
            self::ARCHIVED   => 'secondary',
            self::REJECTED   => 'danger',
        ];
    }

    public static function color(string $key)
    {
        $colors = self::colors();

        if (array_key_exists($key, $colors)) {
            return $colors[$key];
        }

        return 'secondary';
    }
}
