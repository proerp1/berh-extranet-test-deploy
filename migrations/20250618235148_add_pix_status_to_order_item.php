<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPixStatusToOrderItem extends AbstractMigration
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
            ['id' => 109, 'name' => 'Pendente', 'label' => 'badge-secondary', 'categoria' => '23'],
            ['id' => 110, 'name' => 'Erro', 'label' => 'badge-danger', 'categoria' => '23'],
            ['id' => 111, 'name' => 'Aguardando Processamento', 'label' => 'badge-warning', 'categoria' => '23'],
            ['id' => 112, 'name' => 'Enviado', 'label' => 'badge-success', 'categoria' => '23'],
        ];

        $this->table('statuses')->insert($rows)->saveData();

        $table = $this->table('order_items');
        $table->addColumn('pix_status_id', 'integer', ['default' => 109])
            ->update();
    }
}
