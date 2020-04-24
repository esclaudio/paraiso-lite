<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\CustomerValidator;
use App\Models\Customer;
use App\Support\Datatable\Datatable;

class CustomerController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('customers.show');

        return $this->render($response, 'customers.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('customers.show');

        $customer = Customer::findOrFail($args['customer']);

        return $this->render($response, 'customers.show', compact('customer'));
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('customers.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('customers.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $customer = Customer::findOrFail($args['customer']);

        $this->authorize('edit', $customer);

        return $this->createOrEdit($request, $response, $customer);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $customer = Customer::findOrFail($args['customer']);

        $this->authorize('edit', $customer);

        return $this->storeOrUpdate($request, $response, $customer);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $customer = Customer::findOrFail($args['customer']);

        $this->authorize('delete', $customer);

        try {
            $customer->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'customers.show',
                [
                    'customer' => $customer->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $customer->full_name));
        return $this->redirect($request, $response, 'customers.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('customers.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = Customer::select([
            'customers.id',
            'customers.code',
            'customers.name',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('customers.show', ['customer' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, Customer $customer = null): Response
    {
        return $this->render(
            $response,
            'customers.'.($customer? 'edit': 'create'),
            [
                'customer' => $customer
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, Customer $customer = null): Response
    {
        $attributes = CustomerValidator::validate($request);

        if ($customer) {
            $customer->fill($attributes);
        } else {
            $customer = new Customer($attributes);
        }

        $customer->save();

        return $this->redirect($request, $response, 'customers.show', ['customer' => $customer->id]);
    }
}
