<?php

use Phinx\Migration\AbstractMigration;

class InsertRechequeProduct extends AbstractMigration
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
        $products = [
            [
                "id" => 516,
                "status_id" => 1,
                "tipo" => 4,
                "name" => "Recheque",
                "valor" => 10,
                "valor_minimo" => null,
                "descricao" => "",
                "disclaimer" => null,
                "frequency" => "30",
                "created" => null,
                "user_creator_id" => null,
                "data_cancel" => "1901-01-01 00:00:00",
                "usuario_id_cancel" => null,
                "name_include" => null,
                "name_action" => "inside_recheque",
            ],
        ];

        $this->insert('products', $products);
    }
}
