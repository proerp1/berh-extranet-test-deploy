<?php

use Phinx\Migration\AbstractMigration;

class AddExtraFieldsToCustomerUsers extends AbstractMigration
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
        $table = $this->table('customer_users');
        $table->addColumn('rg', 'string', [ // New column
            'null' => true, // or false if it cannot be null
        ])
        ->addColumn('emissor_rg', 'string', [ // New column
            'null' => true, // or false if it cannot be null
        ])
        ->addColumn('emissor_estado', 'string', [ // New column
            'null' => true, // or false if it cannot be null
        ])
        ->addColumn('nome_mae', 'string', [ // New column
            'null' => true, // or false if it cannot be null
        ])
        ->addColumn('sexo', 'string', [ // New column
            'null' => true, // or false if it cannot be null
        ])
        ->addColumn('data_nascimento', 'date', [ // New column
            'null' => true, // or false if it cannot be null
        ])
        ->removeColumn('username')
        ->removeColumn('filial')
        ->removeColumn('acessar_negativacao')
        ->removeColumn('main_user')
        ->update();
    }
}
