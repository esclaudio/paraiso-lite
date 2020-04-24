<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;
use Carbon\Carbon;
use App\Validators\Constraints as MyAssert;
use App\Models\User;
use App\Models\System;
use App\Models\Process;
use App\Models\Indicator;
use App\Models\FrequencyType;

class IndicatorValidator extends Validator
{
    protected function getInput(): array
    {
        $id = $this->request->getAttribute('indicator');

        $input = [
            'system_id'      => (int)$this->request->getParam('system_id'),
            'process_id'     => (int)$this->request->getParam('process_id'),
            'name'           => $this->request->getParam('name'),
            'responsible_id' => (int)$this->request->getParam('responsible_id'),
            'decimals'       => (int)$this->request->getParam('decimals'),
            'unit'           => $this->request->getParam('unit'),
        ];

        // Store

        if ( ! $id) {
            $input = $input + [
                'frequency'      => $this->request->getParam('frequency'),
                'start_date'     => Carbon::createFromFormat(DATE_FORMAT, $this->request->getParam('start_date')),
            ];
        }

        return $input;
    }

    protected function getRules(): array
    {
        $id = $this->request->getAttribute('indicator');

        $rules = [
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

            'name' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Unique([
                    'query' => Indicator::query(),
                    'field' => 'name',
                    'where' => ['id', '<>', $id]
                ]),
                new Assert\Length([
                    'min' => 4,
                    'max' => 190
                ]),
            ]),

            'responsible_id' => new Assert\Required([
                new Assert\NotBlank,
                new MyAssert\Exists([
                    'query' => User::query(),
                    'field' => 'id'
                ]),
            ]),

            'decimals' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\GreaterThanOrEqual([
                    'value' => 0,
                ]),
            ]),

            'unit' => new Assert\Required([
                new Assert\NotBlank,
                new Assert\Length([
                    'min' => 1,
                    'max' => 190
                ]),
            ]),
        ];

        // Store
        
        if ( ! $id) {
            $rules = $rules + [
                'frequency' => new Assert\Required([
                    new Assert\NotBlank,
                    new Assert\GreaterThanOrEqual([
                        'value' => 1,
                    ]),
                ]),

                'start_date' => new Assert\Required([
                    new Assert\NotBlank,
                    new Assert\Date,
                ])
            ];
        }

        return $rules;
    }
}
