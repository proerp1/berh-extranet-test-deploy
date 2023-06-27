<?php


use Phinx\Migration\AbstractMigration;

class AddDataCancelFieldToDistribuicaoCobranca extends AbstractMigration
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
        $table = $this->table("distribuicao_cobranca");
        $table->addColumn("data_cancel", 'datetime', array('default' => '1901-01-01'))
              ->addColumn('usuario_id_cancel', 'integer', array('null' => true))
              ->update();

    }
}
