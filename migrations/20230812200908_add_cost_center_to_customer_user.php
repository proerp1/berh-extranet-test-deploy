<?php

use Phinx\Migration\AbstractMigration;

class AddCostCenterToCustomerUser extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('customer_users');
        $table->addColumn('customer_cost_center_id', 'integer', [ // New column
            'null' => true, // or false if it cannot be null,
            'after' => 'customer_departments_id'
        ])
        ->update();
    }
}
