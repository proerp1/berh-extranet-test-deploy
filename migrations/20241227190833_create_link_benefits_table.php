<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLinkBenefitsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('link_benefits');
        $table->addColumn('file_name', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('created', 'datetime')
              ->addColumn('user_creator_id', 'integer')
              ->addColumn('updated', 'datetime', ['null' => true])
              ->addColumn('user_updated_id', 'integer', ['null' => true])
              ->addColumn('data_cancel', 'datetime', ['default' => '1901-01-01 00:00:00'])
              ->addColumn('usuario_id_cancel', 'integer', ['null' => true])
              ->create();
    }
}
