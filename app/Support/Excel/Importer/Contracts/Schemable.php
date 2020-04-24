<?php

namespace App\Support\Excel\Importer\Contracts;

use App\Excel\Importer\Importer;

interface Schemable
{
    public function getTitle(): string;
    public function getFields(): array;
    public function getImporter(): Importer;
}