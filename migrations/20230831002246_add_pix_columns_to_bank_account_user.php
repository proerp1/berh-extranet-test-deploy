<?php
use Phinx\Migration\AbstractMigration;

class AddPixColumnsToBankAccountUser extends AbstractMigration
{
    public function change()
    {
        $this->table('customer_user_bank_accounts')
            ->addColumn('pix_type', 'string', ['default' => null, 'null' => true, 'after' => 'branch_digit'])
            ->addColumn('pix_id', 'string', ['default' => null, 'null' => true, 'after' => 'pix_type'])
            ->update();
    }
}



