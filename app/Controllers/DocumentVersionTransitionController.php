<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use Carbon\Carbon;
use App\Validators\DocumentVersionTransitionValidator;
use App\Models\DocumentVersionTransition;
use App\Models\DocumentVersion;
use App\Models\DocumentStatus;
use App\Mails\DocumentVersionPublishedMail;
use App\Mails\DocumentVersionPendingMail;

class DocumentVersionTransitionController extends Controller
{
    /**
     * Store
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        /** @var \App\Models\DocumentVersion $version */
        $version = DocumentVersion::where('id', $args['version'])
            ->where('document_id', $args['document'])
            ->firstOrFail();
        
        /** @var \App\Support\Workflow\Workflow $workflow */
        $workflow = $this->get('document.workflow');

        /** @var \Illuminate\Database\Connection $db */
        $db = $this->get('db');
        $db->beginTransaction();

        try {
            $workflow->apply($version, $request->getParam('transition'));
            $db->commit();
        } catch (\Throwable $t) {
            $db->rollBack();
            throw $t;
        }

        return $this->redirect($request, $response, 'documents.show', ['document' => $version->document_id]);
    }
}
