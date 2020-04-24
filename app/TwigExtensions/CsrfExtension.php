<?php

namespace App\TwigExtensions;

use Slim\Csrf\Guard;

class CsrfExtension extends \Twig_Extension
{
    /**
     * @var Slim\Csrf\Guard
     */
    private $guard;

    public function __construct(Guard $guard)
    {
        $this->guard = $guard;

    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('csrf_field', [$this, 'csrfField'], ['is_safe' => ['html']]),
        ];
    }

    public function csrfField(): string
    {
        return '
            <input type="hidden" name="'. $this->guard->getTokenNameKey() .'" value="'. $this->guard->getTokenName() .'">
            <input type="hidden" name="'. $this->guard->getTokenValueKey() .'" value="'. $this->guard->getTokenValue() .'">
        ';
    }
}
