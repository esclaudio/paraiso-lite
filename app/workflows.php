<?php

use Carbon\Carbon;
use App\Support\Workflow\Workflow;
use App\Support\Workflow\TransitionEvent;
use App\Support\Facades\Auth;
use App\Models\DocumentTransition;
use App\Models\DocumentStatus;

$container['document.workflow'] = function ($c) {
    $workflow = new Workflow;

    // States

    foreach (DocumentStatus::all() as $status => $name) {
        $workflow->addState($status, ['name' => $name]);
    }

    // Transtitions

    foreach (DocumentTransition::all() as $transition => $properties) {
        $workflow->addTransition($transition, $properties['from'], $properties['to'], ['title' => $properties['title'], 'class' => $properties['class']]);
    }

    // Events

    $workflow->listen('before', function (TransitionEvent $event) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ( ! $user) {
            $event->setBlocked(true);
            return;
        }

        /** @var \App\Models\DocumentVersion $version */
        $version = $event->getSubject();

        /** @var \App\Models\Document $document */
        $document = $version->document;

        $isResponsible = $user->id === $document->created_by;
        $isReviewer = $user->id === $document->reviewer_id;
        $isApprover = $user->id === $document->approver_id;

        $transitionName = $event->getTransition()->getName();
        $allowed = false;

        switch ($transitionName) {
            case DocumentTransition::DRAFT_TO_REVIEW:
            case DocumentTransition::REJECTED_TO_REVIEW:
            case DocumentTransition::ARCHIVE:
                $allowed = $isResponsible && !$isReviewer;
                break;

            case DocumentTransition::DRAFT_TO_APPROVE:
            case DocumentTransition::REJECTED_TO_APPROVE:
                $allowed = $isResponsible && $isReviewer && !$isApprover;
                break;

            case DocumentTransition::REVIEW_TO_APPROVE:
            case DocumentTransition::REJECT_REVIEW:
                $allowed = $isReviewer;
                break;

            case DocumentTransition::PUBLISH:
            case DocumentTransition::REJECT_APPROVE:
                $allowed = $isApprover;
                break;

            case DocumentTransition::PUBLISH_REVIEW:
                $allowed = $isReviewer && $isApprover;
                break;

            case DocumentTransition::PUBLISH_DRAFT:
                $allowed = $isResponsible && $isReviewer && $isApprover;
                break;
        }

        $event->setBlocked(!$allowed);
    });

    $workflow->listen('after', function (TransitionEvent $event) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \App\Models\DocumentVersion $version */
        $version = $event->getSubject();

        switch ($version->status) {
            case DocumentStatus::TO_APPROVE:
                $version->reviewed_by = $user->id;
                $version->reviewed_at = Carbon::now();
            break;

            case DocumentStatus::PUBLISHED:
                /** @var \App\Models\Document $document */
                $document = $version->document;

                $document->versions()->published()->update(['status' => DocumentStatus::ARCHIVED]);
                $document->unlock();

                if ( ! $version->reviewed_by) {
                    $version->reviewed_by = $user->id;
                    $version->reviewed_at = Carbon::now();
                }

                $version->approved_by = $user->id;
                $version->approved_at = Carbon::now();
            break;
        }

        $version->save();
    });

    return $workflow;
};
