<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateEmailCampanhaTable extends AbstractMigration
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
        $table = $this->table('email_campanha', array('primary_key' => array('email_id')) );
        $table = $this->table('email_campanha');
            $table->addColumn('email_id', 'integer', array('default' => 1))
            ->addColumn('subject', 'string')
            ->addColumn('content', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
            ->addColumn('mail_list', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
            ->addColumn('send', 'integer', array('default' => 0))
            ->addColumn('send_data', 'datetime')
            ->addColumn('user_creator_id', 'integer')
            ->addColumn('data_cancel', 'datetime', array('default' => '1901-01-01'))
            ->addColumn('usuario_id_cancel', 'integer', array('null' => true))
            ->create();
        }
}
