<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use App\Validators\TaskValidator;
use App\Models\User;
use App\Models\Task;

class TaskController extends Controller
{
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
        $task = Task::findOrFail($args['task']);

        return $this->render(
            $response,
            'task/view.twig',
            [
                'task' => $task,
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
    public function store(Request $request, Response $response, array $args): Response
    {
        return $this->storeOrUpdate($request, $response);
    }

    /**
     * Edit
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $task = Task::findOrFail($args['task']);

        $this->authorize('edit', $task);

        $users = User::active()
            ->orderBy('firstname')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        return $this->render(
            $response,
            'task/edit.twig',
            [
                'task'  => $task,
                'users' => $users,
            ]
        );
    }

    /**
     * Update
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $task = Task::findOrFail($args['task']);

        $this->authorize('edit', $task);

        return $this->storeOrUpdate($request, $response, $task);
    }

    /**
     * Complete
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function complete(Request $request, Response $response, array $args): Response
    {
        $task = Task::findOrFail($args['task']);

        $this->authorize('complete', $task);

        $task->complete($this->user);

        return $this->redirect($request, $response, 'task.view', ['task' => $task->id]);
    }

    /**
     * Delete
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $task = Task::findOrFail($args['task']);

        $this->authorize('delete', $task);

        try {
            $task->delete();
        } catch (\Throwable $t) {
            $this->flash->addMessage('error', MSG_ERROR_DELETE);

            return $this->redirect(
                $request,
                $response,
                'task.view',
                [
                    'task' => $task->id,
                ]
            );
        }

        $this->flash->addMessage('success', trans(sprintf(MSG_UI_TASK_DELETED, $task->title)));
        return $this->redirect($request, $response, 'home');
    }

    /**
     * Store or update
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  \App\Models\Task $task
     *
     * @return \Slim\Http\Response
     */
    private function storeOrUpdate(Request $request, Response $response, Task $task = null): Response
    {
        $attributes = TaskValidator::validate($request);

        if ($task) {
            $task->fill($attributes);
        } else {
            $task = new Task($attributes);
        }

        $task->save();

        return $this->redirect($request, $response, 'task.view', ['task' => $task->id]);
    }
}
