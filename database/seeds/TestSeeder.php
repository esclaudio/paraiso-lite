<?php

use Phinx\Seed\AbstractSeed;

class TestSeeder extends AbstractSeed
{
    public function run()
    {
        $this->insertUsers();
    }

    protected function insertUsers()
    {
        $data = [
            [
                'id'        => 1,
                'username'  => 'admin',
                'password'  => password_hash('admin', PASSWORD_DEFAULT),
                'name' => 'Administrator',
                'is_admin'  => true,
            ],
            [
                'id'        => 2,
                'username'  => 'user',
                'password'  => password_hash('user', PASSWORD_DEFAULT),
                'name' => 'John Doe',
            ],
        ];

        $this->insert('users', $data);
    }
}
