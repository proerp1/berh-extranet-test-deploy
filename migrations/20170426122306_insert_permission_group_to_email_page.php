<?php

use Phinx\Migration\AbstractMigration;

class InsertPermissionGroupToEmailPage extends AbstractMigration
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
            ['leitura' => 0, 'escrita' => 0, 'excluir' => 0, 'page_id' => 47, 'group_id' => 6],
            ['leitura' => 1, 'escrita' => 1, 'excluir' => 1, 'page_id' => 47, 'group_id' => 1],
            ['leitura' => 0, 'escrita' => 0, 'excluir' => 0, 'page_id' => 47, 'group_id' => 3],
        ];

        $this->insert('permissions', $rows);

    }
}
