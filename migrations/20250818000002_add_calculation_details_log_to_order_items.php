<?php

use Phinx\Migration\AbstractMigration;

class AddCalculationDetailsLogToOrderItems extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE order_items ADD COLUMN calculation_details_log TEXT NULL");
    }

    public function down()
    {
        $this->execute("ALTER TABLE order_items DROP COLUMN calculation_details_log");
    }
}