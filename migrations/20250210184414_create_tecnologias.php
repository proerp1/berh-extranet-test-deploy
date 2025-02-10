<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTecnologias extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     */
    public function change(): void
    {
        $table = $this->table('tecnologias');
        $table
              ->addColumn('status_id', 'integer', ['default' => 1])
              ->addColumn('name', 'string', ['limit' => 255])
              ->addColumn('description', 'text', ['null' => true])
              ->addColumn('created', 'datetime')
              ->addColumn('user_creator_id', 'integer', ['signed' => false])
              ->addColumn('updated', 'datetime', ['null' => true])
              ->addColumn('user_updated_id', 'integer', ['null' => true, 'signed' => false])
              ->addColumn('data_cancel', 'datetime', ['default' => '1901-01-01 00:00:00'])
              ->addColumn('usuario_id_cancel', 'integer', ['null' => true, 'signed' => false])
              ->addColumn('customer_id', 'integer', ['signed' => false]);

        $table
              ->addIndex(['status_id'])
              ->addIndex(['customer_id'])
              ->create();
    }
}

