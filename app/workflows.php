<?php

use App\Workflow\Workflow;
use App\Workflow\State;
use App\Workflow\TransitionEvent;
use App\Models\DocumentStatus;
use App\Models\DocumentTransition;
use App\Facades\Auth;

$container['workflow'] = function ($c) {
    $workflow = new Workflow;

    // States

    $workflow->addState(DocumentStatus::DRAFT, State::INITIAL_STATE, [
        'title' => MSG_WORKFLOW_DRAFT
    ]);

    $workflow->addState(DocumentStatus::REJECTED, State::INITIAL_STATE, [
        'title' => MSG_WORKFLOW_REJECTED
    ]);

    $workflow->addState(DocumentStatus::TO_REVIEW, State::NORMAL_STATE, [
        'title' => MSG_WORKFLOW_TO_REVIEW
    ]);

    $workflow->addState(DocumentStatus::TO_APPROVE, State::NORMAL_STATE, [
        'title' => MSG_WORKFLOW_TO_APPROVE
    ]);

    $workflow->addState(DocumentStatus::PUBLISHED, State::NORMAL_STATE, [
        'title' => MSG_WORKFLOW_PUBLISHED
    ]);

    $workflow->addState(DocumentStatus::ARCHIVED, State::NORMAL_STATE, [
        'title' => MSG_WORKFLOW_ARCHIVED
    ]);

    // Transitions

    $workflow->addTransition(DocumentTransition::DRAFT_TO_REVIEW, DocumentStatus::DRAFT, DocumentStatus::TO_REVIEW, [
        'title' => MSG_WORKFLOW_SEND_TO_REVIEW,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::DRAFT_TO_APPROVE, DocumentStatus::DRAFT, DocumentStatus::TO_APPROVE, [
        'title' => MSG_WORKFLOW_SEND_TO_APPROVE,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::REVIEW_TO_APPROVE, DocumentStatus::TO_REVIEW, DocumentStatus::TO_APPROVE, [
        'title' => MSG_WORKFLOW_SEND_TO_APPROVE,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::REJECT_REVIEW, DocumentStatus::TO_REVIEW, DocumentStatus::REJECTED, [
        'title' => MSG_WORKFLOW_REJECT,
        'classname' => 'danger',
        'icon' => 'fa fa-undo',
        'require_comments' => true,
    ]);

    $workflow->addTransition(DocumentTransition::REJECT_APPROVE, DocumentStatus::TO_APPROVE, DocumentStatus::REJECTED, [
        'title' => MSG_WORKFLOW_REJECT,
        'classname' => 'danger',
        'icon' => 'fa fa-undo',
        'require_comments' => true,
    ]);

    $workflow->addTransition(DocumentTransition::REJECTED_TO_REVIEW, DocumentStatus::REJECTED, DocumentStatus::TO_REVIEW, [
        'title' => MSG_WORKFLOW_SEND_TO_REVIEW,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::REJECTED_TO_APPROVE, DocumentStatus::REJECTED, DocumentStatus::TO_APPROVE, [
        'title' => MSG_WORKFLOW_SEND_TO_APPROVE,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::PUBLISH, DocumentStatus::TO_APPROVE, DocumentStatus::PUBLISHED, [
        'title' => MSG_WORKFLOW_PUBLISH,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::PUBLISH_DRAFT, DocumentStatus::DRAFT, DocumentStatus::PUBLISHED, [
        'title' => MSG_WORKFLOW_PUBLISH,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::PUBLISH_REVIEW, DocumentStatus::TO_REVIEW, DocumentStatus::PUBLISHED, [
        'title' => MSG_WORKFLOW_PUBLISH,
        'classname' => 'success',
        'icon' => 'fa fa-check',
        'require_comments' => false,
    ]);

    $workflow->addTransition(DocumentTransition::ARCHIVE, DocumentStatus::PUBLISHED, DocumentStatus::ARCHIVED, [
        'title' => MSG_WORKFLOW_ARCHIVE,
        'classname' => 'secondary',
        'icon' => 'fa fa-archive',
        'require_comments' => true,
    ]);

    // Events

    $workflow->listen('before', function (TransitionEvent $event) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \App\Models\DocumentVersion $version */
        $version = $event->getSubject();

        /** @var \App\Models\Document $document */
        $document = $version->document;

        $isResponsible = $user->is_admin || $user->id == $document->responsible_id;
        $isReviewer = $user->is_admin || $user->id == $document->reviewer_id;
        $isApprover = $user->is_admin || $user->id == $document->approver_id;

        $transitionName = $event->getTransition()->getName();
        $allowed = true;

        switch($transitionName) {
            case DocumentTransition::DRAFT_TO_REVIEW:
            case DocumentTransition::REJECTED_TO_REVIEW:
            case DocumentTransition::ARCHIVE:
                $allowed = $isResponsible;
                break;

            case DocumentTransition::DRAFT_TO_APPROVE:
            case DocumentTransition::REJECTED_TO_APPROVE:
                $allowed = $isResponsible && $isReviewer;
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

    return $workflow;
};
