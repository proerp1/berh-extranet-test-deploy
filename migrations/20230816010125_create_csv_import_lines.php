<?php
use Phinx\Migration\AbstractMigration;

class CreateCsvImportLines extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('csv_import_lines');
        $table->addColumn('csv_import_id', 'integer', ['null' => false])
              ->addColumn('cpf', 'string', ['limit' => 20, 'null' => false])
              ->addColumn('benefit_code', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('status_id', 'integer', ['null' => false])
              ->addColumn('user_id', 'integer', ['null' => false])
              ->addColumn('message', 'text', ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}
