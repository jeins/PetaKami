<?php

use Phinx\Seed\AbstractSeed;
use Phalcon\Security;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $security = new Security();
        $this->insert('users', [
            ['email' => 'demo@demo.com', 'password' => $security->hash('demo'), 'full_name' => 'demo user', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
        ]);
    }
}
