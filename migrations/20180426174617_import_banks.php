<?php


use Phinx\Migration\AbstractMigration;

class ImportBanks extends AbstractMigration
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
        $banks = array(
            array( // row #0
                'id' => 1,
                'name' => 'Itaú',
                'created' => '0000-00-00 00:00:00',
                'user_created' => NULL,
                'updated' => '1901-01-01 00:00:00',
                'user_updated' => NULL,
                'data_cancel' => '1901-01-01 00:00:00',
                'usuario_id_cancel' => NULL,
            ),
            array( // row #1
                'id' => 2,
                'name' => 'Banco do Brasil',
                'created' => '0000-00-00 00:00:00',
                'user_created' => NULL,
                'updated' => '1901-01-01 00:00:00',
                'user_updated' => NULL,
                'data_cancel' => '1901-01-01 00:00:00',
                'usuario_id_cancel' => NULL,
            ),
            array( // row #2
                'id' => 3,
                'name' => 'Bradesco',
                'created' => '0000-00-00 00:00:00',
                'user_created' => NULL,
                'updated' => '1901-01-01 00:00:00',
                'user_updated' => NULL,
                'data_cancel' => '1901-01-01 00:00:00',
                'usuario_id_cancel' => NULL,
            ),
            array( // row #3
                'id' => 4,
                'name' => 'Santander',
                'created' => '0000-00-00 00:00:00',
                'user_created' => NULL,
                'updated' => '1901-01-01 00:00:00',
                'user_updated' => NULL,
                'data_cancel' => '1901-01-01 00:00:00',
                'usuario_id_cancel' => NULL,
            ),
            array( // row #4
                'id' => 5,
                'name' => 'Caixa Economica',
                'created' => '0000-00-00 00:00:00',
                'user_created' => NULL,
                'updated' => '1901-01-01 00:00:00',
                'user_updated' => NULL,
                'data_cancel' => '1901-01-01 00:00:00',
                'usuario_id_cancel' => NULL,
            ),
            array( // row #5
                'id' => 6,
                'name' => 'Outros',
                'created' => '0000-00-00 00:00:00',
                'user_created' => NULL,
                'updated' => '1901-01-01 00:00:00',
                'user_updated' => NULL,
                'data_cancel' => '1901-01-01 00:00:00',
                'usuario_id_cancel' => NULL,
            ),
        );

        $this->insert('banks', $banks);
    }
}
