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
                ('systems.show', 'View systems', 'Systems'),
                ('systems.create', 'Create systems', 'Systems'),
                ('systems.edit', 'Edit systems', 'Systems'),
                ('systems.delete', 'Delete systems', 'Systems'),
                ('customers.show', 'Show customers', 'Customers'),
                ('customers.create', 'Create customers', 'Customers'),
                ('customers.edit', 'Edit customers', 'Customers'),
                ('customers.delete', 'Delete customers', 'Customers'),
                ('products.show', 'Show products', 'Products'),
                ('products.create', 'Create products', 'Products'),
                ('products.edit', 'Edit products', 'Products'),
                ('products.delete', 'Delete products', 'Products'),
                ('processes.show', 'Show processes', 'Processes'),
                ('processes.create', 'Create processes', 'Processes'),
                ('processes.delete', 'Delete processes', 'Processes'),
                ('indicators.show', 'Show indicators', 'Indicators'),
                ('indicators.create', 'Create indicators', 'Indicators'),
                ('indicators.delete', 'Delete indicators', 'Indicators'),
                ('documents.show', 'Show documents', 'Documents'),
                ('documents.create', 'Create documents', 'Documents'),
                ('documents.delete', 'Delete documents', 'Documents'),
                ('documents_types.show', 'Show documents types', 'Documents Types'),
                ('documents_types.create', 'Create documents types', 'Documents Types'),
                ('documents_types.edit', 'Edit documents types', 'Documents Types'),
                ('documents_types.delete', 'Delete documents types', 'Documents Types'),
                ('risks.show', 'Show risks', 'Risks'),
                ('risks.create', 'Create risks', 'Risks'),
                ('risks.delete', 'Delete risks', 'Risks'),
                ('risks_likelihoods.show', 'Show risks likelihoods', 'Risks lihelihoods'),
                ('risks_likelihoods.create', 'Create risks likelihoods', 'Risks lihelihoods'),
                ('risks_likelihoods.edit', 'Edit risks likelihoods', 'Risks lihelihoods'),
                ('risks_likelihoods.delete', 'Delete risks likelihoods', 'Risks lihelihoods'),
                ('risks_consequences.show', 'Show risks consequences', 'Risks consequences'),
                ('risks_consequences.create', 'Create risks consequences', 'Risks consequences'),
                ('risks_consequences.edit', 'Edit risks consequences', 'Risks consequences'),
                ('risks_consequences.delete', 'Delete risks consequences', 'Risks consequences'),
                ('risks_levels.show', 'Show risks levels', 'Risks levels'),
                ('risks_levels.create', 'Create risks levels', 'Risks levels'),
                ('risks_levels.edit', 'Edit risks levels', 'Risks levels'),
                ('risks_levels.delete', 'Delete risks levels', 'Risks levels'),
                ('risks_matrix.show', 'Show risks matrix', 'Risks matrix'),
                ('risks_matrix.create', 'Create risks matrix', 'Risks matrix'),
                ('risks_matrix.edit', 'Edit risks matrix', 'Risks matrix'),
                ('risks_matrix.delete', 'Delete risks matrix', 'Risks matrix'),
                ('users.show', 'Show users', 'Users'),
                ('users.create', 'Create users', 'Users'),
                ('users.edit', 'Edit users', 'Users'),
                ('users.delete', 'Delete users', 'Users'),
                ('roles.show', 'Show roles', 'Roles'),
                ('roles.create', 'Create roles', 'Roles'),
                ('roles.edit', 'Edit roles', 'Roles'),
                ('roles.delete', 'Delete roles', 'Roles')
        ");
    }

    public function down()
    {
        $this->schema->drop('permissions');
    }
}
