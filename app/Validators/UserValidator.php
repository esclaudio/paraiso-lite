<?php

namespace App\Validators;

use App\Models\User;
use App\Models\Process;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'username'   => $this->request->getParam('username'),
            'name'       => $this->request->getParam('name'),
            'email'      => $this->request->getParam('email'),
            'is_admin'   => (bool) $this->request->getParam('is_admin', false),
            'is_active'  => (bool) $this->request->getParam('is_active', true),
            'language'   => $this->request->getParam('language'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('user');

        return [
            'username' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => User::query(),
                    'field' => 'username',
                    'where' => ['id', '<>', $id]
                ]),
            ]),

            'name' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
            ]),

            'email' => new Assert\Required([
                new Assert\Email,
            ]),
        ];
    }
}
