<?php

use Phinx\Migration\AbstractMigration;

class AddsCreditReleaseDateToOrder extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('orders');
        $table->addColumn('credit_release_date', 'date', [
            'default' => null,
            'null' => true,
            'after' => 'total', // Adjust the position according to your needs
        ])
        ->addColumn('order_period_to', 'date', [
            'default' => null,
            'null' => true,
            'after' => 'order_period', // Adjust the position according to your needs
        ]);
        $table->update();

        $table = $this->table('orders');
        $table->renameColumn('order_period', 'order_period_from')
              ->update();
    }
}
