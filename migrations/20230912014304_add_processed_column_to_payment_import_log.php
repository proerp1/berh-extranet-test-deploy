<?php
use Phinx\Migration\AbstractMigration;

class AddProcessedColumnToPaymentImportLog extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('payment_import_log');
        $table->addColumn('processed', 'boolean', [
            'default' => false,
            'null' => false,
        ])
        ->update();
    }
}
