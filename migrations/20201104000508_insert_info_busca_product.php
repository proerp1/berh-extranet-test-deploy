<?php


use Phinx\Migration\AbstractMigration;

class InsertInfoBuscaProduct extends AbstractMigration
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
        $rows = [
            ['id' => 449, 'status_id' => 1, 'tipo' => 4, 'name' => 'String - Info Busca', 'valor' => 10, 'name_action' => 'info_busca']
        ];

        $this->insert('products', $rows);

    }
}
