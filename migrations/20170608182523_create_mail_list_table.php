<?php

use Phinx\Migration\AbstractMigration;

class CreateMailListTable extends AbstractMigration
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
		$table = $this->table('mail_lists');
		$table->addColumn('email_campanha_id', 'integer')
					->addColumn('customer_id', 'integer')
					->addColumn('sent', 'boolean')
					->addColumn('created', 'datetime')
					->addColumn('user_creator_id', 'integer')
					->addColumn('updated', 'datetime', array('null' => true))
					->addColumn('user_updated_id', 'integer', array('null' => true))
					->addColumn('data_cancel', 'datetime', array('default' => '1901-01-01'))
					->addColumn('usuario_id_cancel', 'integer', array('null' => true))
					->addIndex(array('customer_id', 'email_campanha_id', 'usuario_id_cancel', 'user_creator_id', 'user_updated_id'))
					->create();

	}
}
