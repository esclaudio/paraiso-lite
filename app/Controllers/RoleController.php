<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\RoleValidator;
use App\Models\Role;
use App\Support\Facades\Cache;
use App\Support\Datatable\Datatable;

class RoleController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('roles.show');

        return $this->render($response, 'roles.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('roles.show');

        $role = Role::findOrFail($args['role']);

        return $this->render($response, 'roles.show', compact('role'));
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('roles.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('roles.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $role = Role::findOrFail($args['role']);

        $this->authorize('edit', $role);

        return $this->createOrEdit($request, $response, $role);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $role = Role::findOrFail($args['role']);

        $this->authorize('edit', $role);

        return $this->storeOrUpdate($request, $response, $role);
    }

    /**
     * Destroy
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $role = Role::findOrFail($args['role']);

        $this->authorize('delete', $role);

        try {
            $role->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'roles.show',
                [
                    'role' => $role->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $role->name));
        return $this->redirect($request, $response, 'roles.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('roles.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = Role::select([
            'roles.id',
            'roles.name',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('roles.show', ['role' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, Role $role = null): Response
    {
        return $this->render(
            $response,
            'roles.'.($role? 'edit': 'create'),
            [
                'role' => $role
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, Role $role = null): Response
    {
        $attributes = RoleValidator::validate($request);

        if ($role) {
            $role->fill($attributes);
        } else {
            $role = new Role($attributes);
        }

        $role->save();

        // TODO:
        // $role->permissions()->sync(
        //     (array)$request->getParam('permission_id')
        // );

        // Cache::forgetAll('user.*.permissions');

        return $this->redirect($request, $response, 'roles.show', ['role' => $role->id]);
    }
}
