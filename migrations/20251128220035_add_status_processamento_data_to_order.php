<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddStatusProcessamentoDataToOrder extends AbstractMigration
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
        $table = $this->table('orders');
        $table
            ->addColumn('status_processamento_data', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('status_processamento_user_id', 'integer', ['null' => true, 'default' => null])
            ->addIndex(['status_processamento_user_id'], ['name' => 'idx_order_status_processamento_user_id'])
            ->update();
    }
}
