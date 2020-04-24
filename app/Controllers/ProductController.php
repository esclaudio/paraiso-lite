<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\ProductValidator;
use App\Models\Product;
use App\Support\Datatable\Datatable;

class ProductController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('products.show');

        return $this->render($response, 'products.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('products.show');

        $product = Product::findOrFail($args['product']);

        return $this->render($response, 'products.show', compact('product'));
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('products.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('products.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $product = Product::findOrFail($args['product']);

        $this->authorize('edit', $product);

        return $this->createOrEdit($request, $response, $product);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $product = Product::findOrFail($args['product']);

        $this->authorize('edit', $product);

        return $this->storeOrUpdate($request, $response, $product);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $product = Product::findOrFail($args['product']);

        $this->authorize('delete', $product);

        try {
            $product->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'products.show',
                [
                    'product' => $product->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $product->full_description));
        return $this->redirect($request, $response, 'products.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('products.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = Product::select([
            'products.id',
            'products.code',
            'products.description',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('products.show', ['product' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, Product $product = null): Response
    {
        return $this->render(
            $response,
            'products.'.($product? 'edit': 'create'),
            [
                'product' => $product
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, Product $product = null): Response
    {
        $attributes = ProductValidator::validate($request);

        if ($product) {
            $product->fill($attributes);
        } else {
            $product = new Product($attributes);
        }

        $product->save();

        return $this->redirect($request, $response, 'products.show', ['product' => $product->id]);
    }
}
