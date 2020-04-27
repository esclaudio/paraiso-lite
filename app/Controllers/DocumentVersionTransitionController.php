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
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\DocumentVersion */
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();
        
        /** @var \App\Workflow\Workflow */
        $workflow = $this->get('document.workflow');

        /** @var \Illuminate\Database\Connection $db */
        $db = $this->get('db');
        $db->beginTransaction();

        try {
            $workflow->apply($version, $request->getParam('transition'));

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

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollBack();
            throw $t;
        }

        return $this->redirect($request, $response, 'documents.show', ['document' => $version->document_id]);
    }
}
