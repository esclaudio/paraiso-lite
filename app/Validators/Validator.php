<?php

namespace App\Validators;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Slim\Http\Request;
use App\Support\Facades\Translator;
use App\Exceptions\ValidationException;

abstract class Validator
{
    /**
     * Request
     * @var \Slim\Http\Request
     */
    protected $request;

    /**
     * Errors
     * @var array
     */
    protected $errors = [];

    protected abstract function getRules(): array;
    protected abstract function getInput(): array;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function validate(Request $request): array
    {
        return (new static($request))->handle();
    }

    public function handle(): array
    {
        $builder = Validation::createValidatorBuilder();
        $builder->setTranslator(Translator::self())->setTranslationDomain('validators');
        $validator = $builder->getValidator();

        $input = $this->getInput();
        $constraint = new Assert\Collection([
            'fields' => $this->getRules(),
            'allowExtraFields' => true,
        ]);

        $violations = $validator->validate($input, $constraint);

        if (count($violations)) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errors = [];

            foreach($violations as $violation) {
                $accessor->setValue($errors, $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ValidationException($errors);
        }

        return $input;
    }
}
