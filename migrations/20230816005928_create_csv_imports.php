<?php
use Phinx\Migration\AbstractMigration;

class CreateCsvImports extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('csv_imports');
        $table->addColumn('customer_id', 'integer', ['null' => false])
              ->addColumn('user_id', 'integer', ['null' => false])
              ->addColumn('file_name', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('status_id', 'integer', ['null' => false])
              ->addColumn('message', 'text', ['null' => true])
              ->addColumn('imported_by_customer', 'boolean', ['null' => false])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('user_creator_id', 'integer', ['null' => true])
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addColumn('user_updated_id', 'integer', ['null' => true])
              ->addColumn('data_cancel', 'datetime', ['default' => '1901-01-01 00:00:00'])
              ->addColumn('usuario_id_cancel', 'integer', ['default' => 0])
              ->create();
    }
}
