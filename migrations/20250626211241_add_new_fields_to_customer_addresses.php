<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddNewFieldsToCustomerAddresses extends AbstractMigration
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
        $rows = [
            ['id' => 113, 'name' => 'Inativo', 'label' => 'badge-warning', 'categoria' => '24'],
            ['id' => 114, 'name' => 'Ativo', 'label' => 'badge-success', 'categoria' => '24'],
        ];

        $this->table('statuses')->insert($rows)->saveData();

        $table = $this->table('customer_addresses');
        $table->addColumn('name', 'string', ['default' => null, 'null' => false])
            ->addColumn('obs', 'text', ['null' => true])
            ->addColumn('customer_user_id', 'integer', ['null' => true])
            ->addColumn('status_id', 'integer', ['null' => false])
            ->addColumn('created', 'datetime')
            ->addColumn('user_creator_id', 'integer', ['signed' => false])
            ->addColumn('updated', 'datetime', ['null' => true])
            ->addColumn('user_updated_id', 'integer', ['null' => true, 'signed' => false])
            ->addColumn('data_cancel', 'datetime', ['default' => '1901-01-01 00:00:00'])
            ->addColumn('usuario_id_cancel', 'integer', ['null' => true, 'signed' => false])
            ->update();
    }
}
