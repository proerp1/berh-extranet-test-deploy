<?php
use Phinx\Migration\AbstractMigration;

class ModifyIncomesTable extends AbstractMigration
{
    public function change()
    {
        // Remove the column billing_monthly_payment_id
        if ($this->table('incomes')->hasColumn('billing_monthly_payment_id')) {
            $this->table('incomes')->removeColumn('billing_monthly_payment_id')->update();
        }

        // Remove the index billing_id
        if ($this->table('incomes')->hasIndex('billing_id')) {
            $this->table('incomes')->removeIndexByName('billing_id')->update();
        }

        // Rename the column billing_id to order_id
        if ($this->table('incomes')->hasColumn('billing_id')) {
            $this->table('incomes')
                ->renameColumn('billing_id', 'order_id')
                ->update();
        }

        // Add index for the column order_id
        if (!$this->table('incomes')->hasIndex('order_id')) {
            $this->table('incomes')
                ->addIndex('order_id', ['name' => 'order_id'])
                ->update();
        }
    }
}
