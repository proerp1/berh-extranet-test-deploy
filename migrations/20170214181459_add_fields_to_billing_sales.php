<?php

use Phinx\Migration\AbstractMigration;

class AddFieldsToBillingSales extends AbstractMigration
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
        $table = $this->table('billing_sales');
        $table->addColumn('faturado_revendas', 'integer', array('after' => 'billing_id', 'default' => 0))
              ->addColumn('faturado_berh', 'integer', array('after' => 'faturado_revendas', 'default' => 0))
              ->update();

    }
}
