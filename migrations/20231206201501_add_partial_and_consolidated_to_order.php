<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPartialAndConsolidatedToOrder extends AbstractMigration
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
        $table->addColumn('is_consolidated', 'integer', [
            'default' => 1,
            'null' => true,
            'after' => 'desconto',
        ])->addColumn('is_partial', 'integer', [
            'default' => 2,
            'null' => true,
            'after' => 'desconto',
        ])->addColumn('economic_group_id', 'integer', [
            'default' => null,
            'null' => true,
            'after' => 'desconto',
        ]);
        $table->update();
    }
}
