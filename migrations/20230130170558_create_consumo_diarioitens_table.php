<?php

use Phinx\Migration\AbstractMigration;

class CreateConsumoDiarioitensTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('consumo_diario_itens');
        $table->addColumn('consumo_diario_id', 'integer')
            ->addColumn('customer_id', 'integer')
            ->addColumn('data', 'date')
            ->addColumn('hora', 'time')
            ->addColumn('logon', 'string')
            ->addColumn('usuario', 'string')
            ->addColumn('documento', 'string')
            ->addColumn('produto', 'string')
            ->addColumn('created', 'datetime')
            ->addColumn('user_creator_id', 'integer')
            ->addColumn('updated', 'datetime', array('null' => true))
            ->addColumn('user_updated_id', 'integer', array('null' => true))
            ->addColumn('data_cancel', 'datetime', array('default' => '1901-01-01'))
            ->addColumn('usuario_id_cancel', 'integer', array('null' => true))
            ->addIndex(array('customer_id'))
            ->addIndex(array('consumo_diario_id'))
            ->addIndex(array('usuario_id_cancel', 'user_creator_id', 'user_updated_id'))
            ->create();

    }
}
