<?php


use Phinx\Migration\AbstractMigration;

class AddNameAndDateColumnsInAtendimentoTable extends AbstractMigration
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
        $atd = $this->table('atendimento');

        $atd->addColumn('name_atendente', 'string', array('null' => true, 'after' => 'read'))
            ->addColumn('data_atendimento', 'datetime', array('null' => true, 'after' => 'name_atendente'))
            ->addColumn('mostrar_cliente', 'integer', array('default' => 1, 'null' => true, 'after' => 'data_atendimento'))
            ->update();
    }
}
