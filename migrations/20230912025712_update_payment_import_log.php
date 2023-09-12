<?php
use Phinx\Migration\AbstractMigration;

class UpdatePaymentImportLog extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('payment_import_log');

        // Remove the existing 'binary_file' column
        if ($table->hasColumn('binary_file')) {
            $table->removeColumn('binary_file')->update();
        }

        // Add a new column 'file_name' to store the file name
        $table->addColumn('file_name', 'string', ['limit' => 255, 'null' => true, 'after' => 'order_data'])
              ->update();
    }

    public function down()
    {
        $table = $this->table('payment_import_log');

        // Remove the 'file_name' column
        if ($table->hasColumn('file_name')) {
            $table->removeColumn('file_name')->update();
        }

        // Add back the 'binary_file' column with its original definition
        $table->addColumn('binary_file', 'binary', ['default' => null, 'null' => true, 'after' => 'order_data'])
              ->update();
    }
}