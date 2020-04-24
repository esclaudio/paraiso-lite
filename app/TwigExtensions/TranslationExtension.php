<?php

namespace App\TwigExtensions;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Symfony\Component\Translation\Translator;

/**
 * Class TranslationExtension
 */
class TranslationExtension extends Twig_Extension
{

    /**
     * Translator
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Translation\Translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('__', [$this, 'trans']),
        ];
    }

    /**
     * Translate callback.
     *
     * @return mixed
     */
    public function trans($key)
    {
        return $this->translator->trans($key);
    }
}
