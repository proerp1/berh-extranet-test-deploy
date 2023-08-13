<?php

use Phinx\Migration\AbstractMigration;

class AddCodeToSupplier extends AbstractMigration
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
        $table = $this->table('suppliers');
        $table->addColumn('code', 'string', [ // New column
            'null' => true, // or false if it cannot be null,
            'after' => 'razao_social'
        ])
        ->update();
    }
}
