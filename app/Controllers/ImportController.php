<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Imports\UserImporter;
use App\Imports\ProviderImporter;
use App\Imports\SourceImporter;
use App\Imports\ProductImporter;
use App\Imports\MaintenancePlanImporter;
use App\Imports\EquipmentImporter;
use App\Imports\CustomerImporter;
use App\Excel\Importer\Importer;
use App\Excel\Importer\HeadingImporter;
use App\Excel\ImportFileSample;
use App\Excel\Excel;

class ImportController extends Controller
{
    /**
     * Importers
     *
     * @var array
     */
    protected $importers = [
        'customer'         => CustomerImporter::class,
        'provider'         => ProviderImporter::class,
        'product'          => ProductImporter::class,
        'equipment'        => EquipmentImporter::class,
        'maintenance_plan' => MaintenancePlanImporter::class,
        'user'             => UserImporter::class,
        'source'           => SourceImporter::class,
    ];

    /**
     * Imports folders
     *
     * @var string
     */
    protected $importsFolder = 'imports';

    /**
     * Valid mime types
     *
     * @var array
     */
	protected $validMime = [
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];
    
    /**
     * Create
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function create(Request $request, Response $response, array $args): Response
    {
        $modelId = $args['model'];

        $this->validateModel($modelId);
        $this->authorize("$modelId.create");
        
        $title = $this->getImporter($modelId)->getTitle();
        $redirect = $this->pathFor("$modelId.index");

        return $this->render(
            $response,
            'import/create.twig',
            [
                'model_id' => $modelId,
                'title'    => $title,
                'redirect' => $redirect,
            ]
        );
    }

    /**
     * Store
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function store($request, $response, array $args): Response
    {
        $modelId = $args['model'];

        $this->validateModel($modelId);
        $this->authorize("$modelId.create");

        if ($uploads = $request->getUploadedFiles()) {
            /**
             * @var \Slim\Http\UploadedFile $upload
             */
            $upload = $uploads['file'] ?? null;
            
            if ($upload && $upload->getError() === UPLOAD_ERR_OK) {
                if ( ! in_array($upload->getClientMediaType(), $this->validMime)) {
                    $this->flash->addMessage('error', MSG_ERROR_INVALID_TYPE);
                } else {
                    $path = storage_put($this->importsFolder, $upload, '');
                    
                    return $this->redirect(
                        $request,
                        $response,
                        'import.view',
                        [
                            'model'    => $modelId,
                            'filename' => pathinfo($path, PATHINFO_FILENAME)
                        ]
                    );
                }
            } else {
                $this->flash->addMessage('error', MSG_ERROR_UPLOAD);
            }
        }

        return $this->redirect($request, $response, 'import.create', ['model' => $modelId]);
    }

    /**
     * View
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $modelId = $args['model'];

        $this->validateModel($modelId);
        $this->authorize("$modelId.create");
        
        $filename = $args['filename'];
        $filePath = storage_path("{$this->importsFolder}/{$filename}");

        if ( ! file_exists($filePath)) {
            $this->flash->addMessage('error', trans('File not found.'));
            return $this->redirect(
                $request,
                $response,
                'import.create',
                [
                    'model' => $modelId,
                ]
            );
        }

        $headings = (new HeadingImporter)->toArray($filePath);
        $rows = (new Excel)->toArray($filePath, 1, 10);
        $importer = $this->getImporter($modelId);
        
        return $this->render(
            $response,
            'import/view.twig',
            [
                'model_id' => $modelId,
                'title'    => $importer->getTitle(),
                'fields'   => $importer->getFields(),
                'filename' => $filename,
                'headings' => $headings,
                'rows'     => $rows,
            ]
        );
    }

    /**
     * Import
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function import(Request $request, Response $response, array $args): Response
    {
        $modelId = $args['model'];

        $this->validateModel($modelId);
        $this->authorize("$modelId.create");

        $filePath = storage_path($this->importsFolder . DS . $args['filename']);

        if ( ! file_exists($filePath)) {
            $this->flash->addMessage('error', trans('File not found.'));
            return $this->redirect(
                $request,
                $response,
                'import.create',
                [
                    'model' => $modelId,
                ]
            );
        }

        $mapping = array_flip(
            array_filter(
                (array)$request->getParam('mapping')
            )
        );
        
        $importer = $this->getImporter($modelId);
        $importer->setMapping($mapping);
        
        $start = microtime(true);
        
        (new Excel)->import($importer, $filePath);

        $elapsed = microtime(true) - $start;
        $count = $importer->getCount();

        unlink($filePath);

        $message = sprintf(trans('%1$s records were imported in %2$s seconds.'), $count, round($elapsed, 2));
        $this->flash->addMessage('success', $message);
        
        return $response->withRedirect(
            $this->pathFor("$modelId.index")
        );
    }

    /**
     * Download
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function download(Request $request, Response $response, array $args): Response
    {
        $modelId = $args['model'];

        $this->validateModel($modelId);

        $importer = $this->getImporter($modelId);

        return (new ImportFileSample($importer))->download($response);
    }

    /**
     * Validate model
     *
     * @param string $modelId
     * 
     * @return void
     */
    private function validateModel(string $modelId)
    {
        if ( ! array_key_exists($modelId, $this->importers)) {
            throw new \Exception('Importer not found');
        }
    }

    /**
     * Get importer
     *
     * @param string $modelId
     * 
     * @return \App\Excel\Importer\Importer
     */
    private function getImporter(string $modelId): Importer
    {
        return new $this->importers[$modelId];
    }
}
