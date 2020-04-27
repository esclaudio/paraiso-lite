<?php

namespace App\TwigExtensions;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Translation\Translator;

/**
 * Class TranslationExtension
 */
class TranslationExtension extends AbstractExtension
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
            new TwigFunction('__', [$this, 'trans']),
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
