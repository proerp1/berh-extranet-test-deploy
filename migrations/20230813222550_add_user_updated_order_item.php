<?php

use Phinx\Migration\AbstractMigration;

class AddUserUpdatedOrderItem extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('order_items');
        $table->addColumn('updated_user_id', 'integer', [
            'default' => null,
            'null' => true,
            'after' => 'updated', // Adjust the position according to your needs
        ]);
        $table->update();
    }
}
