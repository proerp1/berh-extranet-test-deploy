<?php
use Phinx\Migration\AbstractMigration;

final class AddPerfilToCustomerUsers extends AbstractMigration 
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('customer_users');
        $table->addColumn('perfil', 'boolean', ['default' => 1, 'null' => true, 'after' => 'is_admin'])
              ->update();
    }
}
