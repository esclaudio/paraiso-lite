<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Index
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $comments = Comment::byModel($model)
            ->with('createdBy')
            ->orderBy('created_at', 'asc')
            ->get();

        $html = $this->view->fetch('comment/index.twig', [
            'comments' => $comments,
            'model_type' => $args['model_type'],
            'model_id' => $args['model_id'],
        ]);

        return $response->withJson([
            'html' => $html,
            'count' => $comments->count()
        ]);
    }

    /**
     * Store
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * 
     * @return \Slim\Http\Response
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $model = $this->getModel($args['model_type'], $args['model_id']);

        $comment = new Comment([
            'message' => $request->getParam('message')
        ]);

        $model->comments()->save($comment);

        return $response->withJson($comment);
    }
    
    /**
     * Get model
     *
     * @param string $type
     * @param string $id
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function getModel(string $type, string $id): Model
    {
        $class = '\\App\\Models\\' . str_replace('_', '', ucwords($type, '_'));

        return $class::findOrFail($id);
    }
}
