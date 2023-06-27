<?php

use Phinx\Migration\AbstractMigration;

class AddFieldsToEmailCampanha extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('email_campanha');
        $table->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('null' => true))
              ->addColumn('user_updated_id', 'integer', array('null' => true))
              ->update();

        $table->changeColumn('user_creator_id', 'integer', ['after' => 'created'])
              ->changeColumn('data_cancel', 'datetime', array('after' => 'user_updated_id', 'default' => '1901-01-01'))
              ->changeColumn('usuario_id_cancel', 'integer', array('after' => 'data_cancel', 'null' => true))
              ->addIndex(array('user_updated_id', 'usuario_id_cancel', 'user_creator_id'))
              ->update();

    }
}
