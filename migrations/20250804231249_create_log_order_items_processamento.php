<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLogOrderItemsProcessamento extends AbstractMigration
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
        $table = $this->table('log_order_items_processamento');
        $table
            ->addColumn('order_item_id', 'integer', ['null' => true])
            ->addColumn('status_processamento', 'string', ['limit' => 300, 'null' => true])
            ->addColumn('pedido_operadora', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('data_entrega', 'datetime', ['null' => true])
            ->addColumn('motivo_processamento', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('created', 'datetime')
            ->addColumn('user_creator_id', 'integer')
            ->addColumn('updated', 'datetime', ['null' => true])
            ->addColumn('user_updated_id', 'integer', ['null' => true])
            ->addColumn('data_cancel', 'datetime', ['null' => true, 'default' => '1901-01-01 00:00:00'])
            ->addColumn('usuario_id_cancel', 'integer', ['null' => true])
            ->create();
    }
}
