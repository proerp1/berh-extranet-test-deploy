<?php


use Phinx\Migration\AbstractMigration;

class AddColunasToIncomeParaCnab extends AbstractMigration
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
        $atd = $this->table('incomes');

        $atd->addColumn('cnab_gerado', 'integer', array('default' => 2, 'null' => true))
            ->addColumn('cnab_lote_id', 'integer', array('null' => true))
            ->addColumn('cnab_num_sequencial', 'string', array('null' => true))
            ->update();
    }
}
