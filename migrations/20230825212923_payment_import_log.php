<?php
use Phinx\Migration\AbstractMigration;

class PaymentImportLog extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('payment_import_log');
        $table->addColumn('order_data', 'json', ['default' => null, 'null' => false])
            ->addColumn('binary_file', 'blob', ['default' => null, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->create();
    }
}