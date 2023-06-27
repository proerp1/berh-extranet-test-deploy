<?php


use Phinx\Migration\AbstractMigration;

class ImportCnabStatuses extends AbstractMigration
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
        $statuses = array(
            array( // row #43
                'id' => 46,
                'name' => 'Aguardando retorno',
                'label' => 'label-warning',
                'categoria' => 12,
            ),
            array( // row #44
                'id' => 47,
                'name' => 'Importado',
                'label' => 'label-success',
                'categoria' => 12,
            ),
            array( // row #45
                'id' => 48,
                'name' => 'Aguardando retorno',
                'label' => 'label-warning',
                'categoria' => 13,
            ),
            array( // row #46
                'id' => 49,
                'name' => 'Erro',
                'label' => 'label-danger',
                'categoria' => 13,
            ),
            array( // row #47
                'id' => 50,
                'name' => 'Sucesso',
                'label' => 'label-success',
                'categoria' => 13,
            ),
        );

        $this->insert('statuses', $statuses);

    }
}
