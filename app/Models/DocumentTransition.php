<?php

namespace App\Models;

abstract class DocumentTransition
{
    const DRAFT_TO_REVIEW     = 'draft_to_review';
    const DRAFT_TO_APPROVE    = 'draft_to_approve';
    const REVIEW_TO_APPROVE   = 'review_to_approve';

    const REJECT_REVIEW       = 'reject_review';
    const REJECT_APPROVE      = 'reject_approve';

    const REJECTED_TO_REVIEW  = 'rejected_to_review';
    const REJECTED_TO_APPROVE = 'rejected_to_approve';

    const PUBLISH             = 'publish';
    const PUBLISH_DRAFT       = 'publish_draft';
    const PUBLISH_REVIEW      = 'publish_review';

    const ARCHIVE             = 'archive';

    public static function all(): array
    {
        return [
            self::DRAFT_TO_REVIEW => [
                'title' => trans('Send to review'),
                'class' => 'success',
                'from'  => DocumentStatus::DRAFT,
                'to'    => DocumentStatus::TO_REVIEW,
            ],
            self::DRAFT_TO_APPROVE => [
                'title' => trans('Send to approve'),
                'class' => 'success',
                'from'  => DocumentStatus::DRAFT,
                'to'    => DocumentStatus::TO_APPROVE,
            ],
            self::REVIEW_TO_APPROVE => [
                'title' => trans('Send to approve'),
                'class' => 'success',
                'from'  => DocumentStatus::TO_REVIEW,
                'to'    => DocumentStatus::TO_APPROVE,
            ],
            self::REJECT_REVIEW => [
                'title' => trans('Reject'),
                'class' => 'danger',
                'from'  => DocumentStatus::TO_REVIEW,
                'to'    => DocumentStatus::REJECTED,
            ],
            self::REJECT_APPROVE => [
                'title' => trans('Reject'),
                'class' => 'danger',
                'from'  => DocumentStatus::TO_APPROVE,
                'to'    => DocumentStatus::REJECTED,
            ],
            self::REJECTED_TO_REVIEW => [
                'title' => trans('Send to review'),
                'class' => 'success',
                'from'  => DocumentStatus::REJECTED,
                'to'    => DocumentStatus::TO_REVIEW,
            ],
            self::REJECTED_TO_APPROVE => [
                'title' => trans('Send to approve'),
                'class' => 'success',
                'from'  => DocumentStatus::REJECTED,
                'to'    => DocumentStatus::TO_APPROVE,
            ],
            self::PUBLISH => [
                'title' => trans('Publish'),
                'class' => 'success',
                'from'  => DocumentStatus::TO_APPROVE,
                'to'    => DocumentStatus::PUBLISHED,
            ],
            self::PUBLISH_DRAFT => [
                'title' => trans('Publish'),
                'class' => 'success',
                'from'  => DocumentStatus::DRAFT,
                'to'    => DocumentStatus::PUBLISHED,
            ],
            self::PUBLISH_REVIEW => [
                'title' => trans('Publish'),
                'class' => 'success',
                'from'  => DocumentStatus::TO_REVIEW,
                'to'    => DocumentStatus::PUBLISHED,
            ],
            self::ARCHIVE => [
                'title' => trans('Archive'),
                'class' => 'secondary',
                'from'  => DocumentStatus::PUBLISHED,
                'to'    => DocumentStatus::ARCHIVED,
            ],
        ];
    }

    public static function description(string $key): string
    {
        return self::all()[$key]['title'] ?? $key;
    }
}
