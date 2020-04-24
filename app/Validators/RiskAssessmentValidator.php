<?php

namespace App\Validators;

use App\Models\RiskLikelihood;
use App\Models\RiskConsequence;
use App\Validators\Constraints as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RiskAssessmentValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'risk_likelihood_id'  => $this->request->getParam('risk_likelihood_id'),
            'risk_consequence_id' => $this->request->getParam('risk_consequence_id'),
            'conclusions'         => $this->request->getParam('conclusions'),
        ];
    }

    protected function getRules(): array
    {
        return [
            'risk_likelihood_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => RiskLikelihood::query(),
                    'field' => 'id'
                ]),
            ]),

            'risk_consequence_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => RiskConsequence::query(),
                    'field' => 'id'
                ]),
            ]),
        ];
    }
}
