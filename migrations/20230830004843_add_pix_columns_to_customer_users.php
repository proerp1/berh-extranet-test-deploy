<?php

use Phinx\Migration\AbstractMigration;

class AddPixColumnsToCustomerUsers extends AbstractMigration
{
    public function change()
    {
        $this->table('customer_users')
            ->addColumn('pix_type', 'string', ['default' => null, 'null' => true, 'after' => 'email'])
            ->addColumn('pix_id', 'string', ['default' => null, 'null' => true, 'after' => 'pix_type'])
            ->update();
    }
}
