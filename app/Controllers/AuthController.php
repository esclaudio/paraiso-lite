<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Carbon\Carbon;
use App\Validators\UserPasswordValidator;

class AuthController extends Controller
{
    public function login(Request $request, Response $response): Response
    {
        if ($request->isGet()) {
            return $this->render($response, 'login');
        }

        $username = $request->getParam('username', '');
        $password = $request->getParam('password', '');

        if ($this->auth->attempt($username, $password)) {
            $user = $this->auth->user();
            $remember = $request->getParam('remember', false);

            if ($remember) {
                if (empty($user->remember_token)) {
                    $user->generateRememberToken();
                }

                $cookie = SetCookie::create($this->auth->getRememberCookieName())
                    ->withValue($this->auth->getRememberCookieValue())
                    ->withExpires(new \DateTime(REMEMBER_TIME));

                $response = FigResponseCookies::set($response, $cookie);
            }

            $user->addOperation('login');

            return $response->withRedirect(
                $this->auth->getRedirectUrl() ?: $this->pathFor('home')
            );
        }

        $this->flash->addMessage('error', MSG_ERROR_LOGIN);
        return $this->redirect($request, $response, 'login');
    }

    public function logout(Request $request, Response $response): Response
    {
        if ($this->auth->check()) {
            $user = $this->auth->user();

            $user->addOperation('logout');
            $user->removeRememberToken();
            $this->auth->logout();
        }

        $response = FigResponseCookies::expire($response, $this->auth->getRememberCookieName());

        return $this->redirect($request, $response, 'home');
    }

    public function changePassword(Request $request, Response $response): Response
    {
        if ($request->isGet()) {
            return $this->render($response, 'user.change_password');
        }

        $user = $this->user;
        $attributes = UserPasswordValidator::validate($request);

        if ( ! password_verify($attributes['current_password'], $user->password)) {
            return $response->withJson([
                'current_password' =>  'Contraseña incorrecta'
            ], 400);
        }

        if ($attributes['current_password'] == $attributes['password']) {
            return $response->withJson([
                'password' =>  'No puede usar la misma contraseña'
            ], 400);
        }

        $user->setPassword($attributes['password']);
        $user->password_expiration_date = Carbon::now()->addMonths(PASSWORD_DURATION);
        $user->save();

        $this->flash->addMessage('success', MSG_UI_PASSWORD_CHANGED);
        $this->auth->logout();

        return $this->redirect($request, $response, 'login');
    }
}
