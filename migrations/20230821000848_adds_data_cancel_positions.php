<?php

use Phinx\Migration\AbstractMigration;

class AddsDataCancelPositions extends AbstractMigration
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
        $table = $this->table('customer_positions');

        // Add the 'data_cancel' field
        $table->addColumn('data_cancel', 'datetime', [
            'default' => '1901-01-01 00:00:00',
            'null' => true,
            'after' => 'name',
        ]);

        // Add the 'usuario_id_cancel' field
        $table->addColumn('usuario_id_cancel', 'integer', [
            'default' => 0,
            'null' => true,
            'after' => 'data_cancel',
        ]);

        $table->update();
    }
}

