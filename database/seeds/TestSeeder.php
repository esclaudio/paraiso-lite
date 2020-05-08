<?php

use Phinx\Seed\AbstractSeed;
use Carbon\Carbon;

class TestSeeder extends AbstractSeed
{
    public function run()
    {
        $this->insertUsers();
        $this->insertSystems();
        $this->insertProcesses();
        $this->insertDocumentsTypes();
    }

    protected function insertUsers()
    {
        $data = [
            [
                'id'       => 1,
                'username' => 'admin',
                'password' => bcrypt('admin'),
                'name'     => 'Administrator',
                'is_admin' => true,
            ],
            [
                'id'       => 2,
                'username' => 'user',
                'password' => bcrypt('user'),
                'name'     => 'John Doe',
            ],
        ];

        $this->insert('users', $data);
    }

    protected function insertSystems()
    {
        $data = [
            [
                'id'         => 1,
                'name'       => 'ISO 9001',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id'         => 2,
                'name'       => 'ISO 14001',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
        ];

        $this->insert('systems', $data);
    }

    protected function insertProcesses()
    {
        $data = [
            [
                'id'         => 1,
                'name'       => 'Sales',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id'         => 2,
                'name'       => 'Purchasing',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id'         => 3,
                'name'       => 'Production',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id'         => 4,
                'name'       => 'Human Resources',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
        ];

        $this->insert('processes', $data);
    }

    protected function insertDocumentsTypes()
    {
        $data = [
            [
                'id'          => 1,
                'name'        => 'Procedures',
                'prefix'      => 'PR',
                'next_number' => 1,
                'created_by'   => 1,
                'created_at'   => Carbon::now(),
            ],
            [
                'id'          => 2,
                'name'        => 'Work Instructions',
                'prefix'      => 'WI',
                'next_number' => 1,
                'created_by'  => 1,
                'created_at'  => Carbon::now(),
            ],
        ];

        $this->insert('documents_types', $data);
    }
}
