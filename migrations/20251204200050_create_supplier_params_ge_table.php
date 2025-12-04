<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSupplierParamsGeTable extends AbstractMigration
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
        $table = $this->table('supplier_params_ge');
        $table
            ->addColumn('customer_id', 'integer')
            ->addColumn('status_id', 'integer')
            ->addColumn('supplier_id', 'integer')
            ->addColumn('tickets', 'integer')
            ->addColumn('minimum_purchase', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('created', 'datetime')
            ->addColumn('user_creator_id', 'integer')
            ->addColumn('updated', 'datetime', ['null' => true])
            ->addColumn('user_updated_id', 'integer', ['null' => true])
            ->addColumn('data_cancel', 'datetime', ['null' => true, 'default' => '1901-01-01 00:00:00'])
            ->addColumn('usuario_id_cancel', 'integer', ['null' => true])
            ->create();
    }
}
