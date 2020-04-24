<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validators\Constraints as MyAssert;
use App\Models\User;
use App\Models\System;
use App\Models\SwotItem;
use App\Models\Source;
use App\Models\RiskType;
use App\Models\RiskTreatmentType;
use App\Models\RiskLikelihood;
use App\Models\RiskConsequence;
use App\Models\Risk;
use App\Models\Process;

class RiskUpdateValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'source_id'              => $this->request->getParam('source_id'),
            'system_id'              => $this->request->getParam('system_id'),
            'process_id'             => $this->request->getParam('process_id'),
            'description'            => $this->request->getParam('description'),
            'impact'                 => $this->request->getParam('impact'),
            'responsible_id'         => $this->request->getParam('responsible_id'),
            'risk_likelihood_id'     => $this->request->getParam('risk_likelihood_id'),
            'risk_consequence_id'    => $this->request->getParam('risk_consequence_id'),
            'risk_treatment_type_id' => $this->request->getParam('risk_treatment_type_id'),
            'observations'           => $this->request->getParam('observations'),
        ];
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('risk');
        
        return [
            'source_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => Source::query(),
                    'field' => 'id'
                ]),
            ]),

            'system_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => System::query(),
                    'field' => 'id'
                ]),
            ]),

            'process_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => Process::query(),
                    'field' => 'id'
                ]),
            ]),

            'description' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
                new MyAssert\Unique([
                    'query' => Risk::query(),
                    'field' => 'description',
                    'where' => ['id', '<>', $id]
                ]),
            ]),

            'responsible_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => User::query(),
                    'field' => 'id'
                ]),
            ]),

            'impact' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ])
            ]),

            'risk_likelihood_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => RiskLikelihood::where('risk_type_id', $this->request->getParam('risk_type_id')),
                    'field' => 'id',
                ])
            ]),

            'risk_consequence_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => RiskConsequence::where('risk_type_id', $this->request->getParam('risk_type_id')),
                    'field' => 'id',
                ])
            ]),

            'risk_treatment_type_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => RiskTreatmentType::query(),
                    'field' => 'id',
                ])
            ]),
        ];
    }
}
