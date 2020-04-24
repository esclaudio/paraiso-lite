<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;
use Carbon\Carbon;
use App\Validators\Constraints as MyAssert;
use App\Models\User;
use App\Models\System;
use App\Models\Product;
use App\Models\Process;
use App\Models\Nonconformity;
use App\Models\Customer;

class NonconformityValidator extends Validator
{
    protected function getInput(): array
    {
        return [
            'system_id'             => $this->request->getParam('system_id'),
            'process_id'            => $this->request->getParam('process_id'),
            'description'           => $this->request->getParam('description'),
            'occurrence_date'       => Carbon::createFromFormat(DATE_FORMAT, $this->request->getParam('occurrence_date')),
            'customer_id'           => $this->request->getParam('customer_id'),
            'product_id'            => $this->request->getParam('product_id'),
            'quantity'              => $this->request->getParam('quantity'),
        ];
    }

    protected function getRules(): array
    {
        return [
            'system_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => System::query(),
                    'field' => 'id'
                ]),
            ]),
            
            'process_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => Process::query(),
                    'field' => 'id'
                ]),
            ]),

            'description' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Length([
                    'min' => 4
                ]),
            ]),

            'customer_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => Customer::query(),
                    'field' => 'id'
                ]),
            ]),

            'product_id' => new Assert\Required([
                new MyAssert\Exists([
                    'query' => Product::query(),
                    'field' => 'id'
                ]),
            ]),
        ];
    }
}
