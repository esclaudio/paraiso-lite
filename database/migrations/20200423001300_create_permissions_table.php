<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        $this->schema->create('permissions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('description');
            $table->string('module');
        });

        $this->connection->statement("
            INSERT INTO permissions (id, description, module) VALUES
                ('systems.show', 'Show', 'Systems'),
                ('systems.create', 'Create', 'Systems'),
                ('systems.edit', 'Edit', 'Systems'),
                ('systems.destroy', 'Delete', 'Systems'),
                ('customers.show', 'Show', 'Customers'),
                ('customers.create', 'Create', 'Customers'),
                ('customers.edit', 'Edit', 'Customers'),
                ('customers.destroy', 'Delete', 'Customers'),
                ('products.show', 'Show', 'Products'),
                ('products.create', 'Create', 'Products'),
                ('products.edit', 'Edit', 'Products'),
                ('products.destroy', 'Delete', 'Products'),
                ('processes.show', 'Show', 'Processes'),
                ('processes.create', 'Create', 'Processes'),
                ('processes.destroy', 'Delete', 'Processes'),
                ('indicators.show', 'Show', 'Indicators'),
                ('indicators.create', 'Create', 'Indicators'),
                ('indicators.destroy', 'Delete', 'Indicators'),
                ('nonconformities.show', 'Show', 'Non Conformities'),
                ('nonconformities.create', 'Create', 'Non Conformities'),
                ('nonconformities.destroy', 'Delete', 'Non Conformities'),
                ('documents.show', 'Show', 'Documents'),
                ('documents.create', 'Create', 'Documents'),
                ('documents.destroy', 'Delete', 'Documents'),
                ('documents_types.show', 'Show', 'Documents Types'),
                ('documents_types.create', 'Create', 'Documents Types'),
                ('documents_types.edit', 'Edit', 'Documents Types'),
                ('documents_types.destroy', 'Delete', 'Documents Types'),
                ('risks.show', 'Show', 'Risks'),
                ('risks.create', 'Create', 'Risks'),
                ('risks.destroy', 'Delete', 'Risks'),
                ('risks_likelihoods.show', 'Show', 'Risks lihelihoods'),
                ('risks_likelihoods.create', 'Create', 'Risks lihelihoods'),
                ('risks_likelihoods.edit', 'Edit', 'Risks lihelihoods'),
                ('risks_likelihoods.destroy', 'Delete', 'Risks lihelihoods'),
                ('risks_consequences.show', 'Show', 'Risks consequences'),
                ('risks_consequences.create', 'Create', 'Risks consequences'),
                ('risks_consequences.edit', 'Edit', 'Risks consequences'),
                ('risks_consequences.destroy', 'Delete', 'Risks consequences'),
                ('risks_levels.show', 'Show', 'Risks levels'),
                ('risks_levels.create', 'Create', 'Risks levels'),
                ('risks_levels.edit', 'Edit', 'Risks levels'),
                ('risks_levels.destroy', 'Delete', 'Risks levels'),
                ('risks_matrix.show', 'Show', 'Risks matrix'),
                ('risks_matrix.create', 'Create', 'Risks matrix'),
                ('risks_matrix.edit', 'Edit', 'Risks matrix'),
                ('risks_matrix.destroy', 'Delete', 'Risks matrix'),
                ('users.show', 'Show', 'Users'),
                ('users.create', 'Create', 'Users'),
                ('users.edit', 'Edit', 'Users'),
                ('users.destroy', 'Delete', 'Users'),
                ('roles.show', 'Show', 'Roles'),
                ('roles.create', 'Create', 'Roles'),
                ('roles.edit', 'Edit', 'Roles'),
                ('roles.destroy', 'Delete', 'Roles')
        ");
    }

    public function down()
    {
        $this->schema->drop('permissions');
    }
}
