<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColumnsToCustomerSupplierLogins extends AbstractMigration
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
    public function change(): void
    {
        $table = $this->table('customer_supplier_logins');
        $table->addColumn('observation', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('status_id', 'integer', ['limit' => 11, 'null' => false, 'default' => 1])
              ->update();
    }
}
