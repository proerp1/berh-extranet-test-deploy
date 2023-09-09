<?php
use Phinx\Migration\AbstractMigration;

class AddsGoalToUser extends AbstractMigration
{
    public function change()
    {
        $this->table('users')
            ->addColumn('sales_goal', 'double', ['default' => 0, 'null' => true, 'after' => 'is_seller'])
            ->update();
    }
}



