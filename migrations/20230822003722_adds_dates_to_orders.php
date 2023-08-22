<?php

use Phinx\Migration\AbstractMigration;

class AddsDatesToOrders extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('orders');
        $table->addColumn('validation_date', 'date', [
            'default' => null,
            'null' => true,
            'after' => 'created',
        ])
        ->addColumn('issuing_date', 'date', [
            'default' => null,
            'null' => true,
            'after' => 'validation_date',
        ])
        ->addColumn('payment_date', 'date', [
            'default' => null,
            'null' => true,
            'after' => 'issuing_date',
        ]);
        $table->update();
    }
}
