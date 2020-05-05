<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\UserValidator;
use App\Models\User;
use App\Models\Role;
use App\Models\Process;
use App\Models\Language;
use App\Support\Facades\Cache;
use App\Support\Datatable\Datatable;

class UserController extends Controller
{
    /**
     * Index
     */
    public function index(Request $request, Response $response): Response
    {
        $this->authorize('users.show');

        return $this->render($response, 'users.index');
    }

    /**
     * Show
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $this->authorize('users.show');

        $user = User::findOrFail($args['user']);

        return $this->render(
            $response,
            'users.show',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * Create
     */
    public function create(Request $request, Response $response): Response
    {
        $this->authorize('users.create');

        return $this->createOrEdit($request, $response);
    }

    /**
     * Store
     */
    public function store(Request $request, Response $response): Response
    {
        $this->authorize('users.create');

        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['user']);

        $this->authorize('edit', $user);

        return $this->createOrEdit($request, $response, $user);
    }

    /**
     * Update
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['user']);

        $this->authorize('edit', $user);

        return $this->storeOrUpdate($request, $response, $user);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['user']);

        $this->authorize('edit', $user);

        $user->resetPassword();
        
        $this->flash->addMessage('success', sprintf(trans('The password was reset to "%s". User must login and change it.'), DEFAULT_PASSWORD));
        
        return $this->redirect($request, $response, 'users.show', ['user' => $user->id]);
    }

    /**
     * Change avatar
     */
    public function changeAvatar(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['user']);

        $this->authorize('edit', $user);

        $uploadedFiles = $request->getUploadedFiles();

        if (isset($uploadedFiles['file'])) {
            $uploadedFile = $uploadedFiles['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $user->uploadAvatar($uploadedFile, $this->get('image'));
            }
        }
        
        return $response->withJson([
            'url' => $user->avatar_url,
        ]);
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['user']);

        $this->authorize('edit', $user);

        $user->deleteAvatar();

        return $response->withJson([
            'url' => $user->avatar_url,
        ]);
    }

    /**
     * Delete
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['user']);

        $this->authorize('delete', $user);

        try {
            $user->delete();    
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'users.show',
                [
                    'user' => $user->id,
                ]
            );
        }
        
        $this->flash->addMessage('success', sprintf(MSG_DELETED, $user->name));
        return $this->redirect($request, $response, 'users.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request, Response $response): Response
    {
        $this->authorize('users.show');

        /** @var \Slim\Router */
        $router = $this->get('router');

        /** @var \Illuminate\Database\Query\Builder */
        $query = User::select([
            'users.id',
            'users.name',
        ])->getQuery();

        return (new Datatable($query, $request, $response)) 
            ->addColumn('show_url', function ($row) use ($router) {
                return $router->pathFor('users.show', ['user' => $row->id]);
            })
            ->response();
    }

    /**
     * Create or edit
     */
    private function createOrEdit(Request $request, Response $response, User $user = null): Response
    {
        if ($user) {
            $userRoles = $user->roles()
                ->pluck('id')
                ->toArray();
        } else {
            $userRoles = [];
        }

        $roles = Role::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return $this->render(
            $response,
            'users.'.($user? 'edit': 'create'),
            [
                'user'       => $user,
                'roles'      => $roles,
                'user_roles' => $userRoles,
                'languages'  => Language::all(),
            ]
        );
    }

    /**
     * Store or update
     */
    private function storeOrUpdate(Request $request, Response $response, User $user = null): Response
    {
        $attributes = UserValidator::validate($request);

        if ($user) {
            $user->fill($attributes);
        } else {
            $user = new User($attributes);
        }

        $user->save();

        $user->roles()->sync(
            (array)$request->getParam('roles')
        );
        
        Cache::forget("users.{$user->id}.permissions");

        return $this->redirect($request, $response, 'users.show', ['user' => $user->id]);
    }
}
