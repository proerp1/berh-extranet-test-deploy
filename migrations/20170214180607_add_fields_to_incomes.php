<?php

use Phinx\Migration\AbstractMigration;

class AddFieldsToIncomes extends AbstractMigration
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
        $table = $this->table('incomes');
        $table->addColumn('billing_sales_id', 'integer', array('after' => 'parcela', 'null' => true))
              ->addColumn('resale_id', 'integer', array('after' => 'billing_sales_id', 'null' => true))
              ->addColumn('seller_id', 'integer', array('after' => 'resale_id', 'null' => true))
              ->update();

    }
}
