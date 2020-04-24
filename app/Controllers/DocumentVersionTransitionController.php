<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\DocumentVersion;
use App\Models\DocumentVersionTransition;
use App\Models\DocumentStatus;
use App\Mails\DocumentVersionPendingMail;
use App\Mails\DocumentVersionPublishedMail;
use App\Validators\DocumentVersionTransitionValidator;

class DocumentVersionTransitionController extends Controller
{
    /**
     * Store
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     *
     * @return \Slim\Http\Response
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();

        $attributes = DocumentVersionTransitionValidator::validate($request);

        /** @var \App\Workflow\Workflow $workflow */
        $workflow = $this->get('workflow');

        if ( ! $workflow->can($version, $attributes['transition'])) {
            return $response->withJson([
                'error' => 'Invalid transition'
            ], 400);
        }

        /** @var \Illuminate\Database\Connection $db */
        $db = $this->get('db');
        $db->beginTransaction();

        try {
            $workflow->apply($version, $attributes['transition']);

            switch ($version->status) {
                case DocumentStatus::PUBLISHED:
                    $version->publish();
                    break;
                
                case DocumentStatus::ARCHIVED:
                    $version->archive();
                    break;

                default:
                    $version->save();
            }
            
            $transition = new DocumentVersionTransition($attributes);
            
            $version->transitions()->save($transition);

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollBack();
            throw $t;
        }

        switch ($version->status) {
            // If version is pending, send mail to pending responsible
            case DocumentStatus::TO_REVIEW:
            case DocumentStatus::TO_APPROVE:
            case DocumentStatus::REJECTED:
                $this->sendMail($version->responsible, new DocumentVersionPendingMail($version));
                break;
            
            // If version is published, send mail to document responsible
            case DocumentStatus::PUBLISHED:
                $this->sendMail($version->document->responsible, new DocumentVersionPublishedMail($version));
                break;
        }

        return $this->redirect($request, $response, 'document.view', ['document' => $version->document_id]);
    }
}
