<?php

use Phinx\Migration\AbstractMigration;

class UpdatesCustomerUserBanks extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('customer_user_bank_accounts');
        $table->addColumn('bank_code_id', 'integer', [
            'null' => false,
            'after' => 'account_type_id',
        ])
        ->removeColumn('bank_name')
        ->removeColumn('bank_code')
        ->update();
    }
}
