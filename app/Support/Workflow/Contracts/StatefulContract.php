<?php

namespace App\Support\Workflow\Contracts;

interface StatefulContract
{
    public function getState() : string;
    public function setState(string $state);
}
