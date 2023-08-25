<?php
use Phinx\Migration\AbstractMigration;

class OrderUpdateImportLog extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('order_update_import_log');
        $table->addColumn('order_data', 'json', ['default' => null, 'null' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->create();
    }
}