<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrderDiscountBatch extends AbstractMigration
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
        $batchesTable = $this->table('order_discount_batches');
        $batchesTable
            ->addColumn('order_id', 'integer', ['null' => false])
            ->addColumn('discount_type', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('observacao', 'text', ['null' => true])
            ->addColumn('valor_total', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false, 'default' => 0.00])
            ->addColumn('quantidade_pedidos', 'integer', ['null' => false,'default' => 0])
            ->addColumn('user_creator_id', 'integer', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('updated', 'datetime', ['null' => true])
            ->addColumn('user_updated_id', 'integer', ['null' => true])
            ->addColumn('data_cancel', 'datetime', ['null' => false, 'default' => '1901-01-01 00:00:00'])
            ->addColumn('usuario_id_cancel', 'integer', ['null' => true])
            ->addIndex(['order_id'])
            ->addIndex(['discount_type'])
            ->addIndex(['data_cancel'])
            ->addIndex(['created'])
            ->create();

        $itemsTable = $this->table('order_discount_batch_items');
        $itemsTable
            ->addColumn('batch_id', 'integer', ['null' => false])
            ->addColumn('order_parent_id', 'integer', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addIndex(['batch_id'])
            ->addIndex(['order_parent_id'])
            ->addIndex(['batch_id', 'order_parent_id'], ['name' => 'idx_batch_order'])
            ->create();
    }
}
