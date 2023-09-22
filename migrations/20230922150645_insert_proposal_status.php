<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InsertProposalStatus extends AbstractMigration
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
        $table = $this->table('statuses');

        $rows = [
            ['id' => 91, 'name' => 'Aberta', 'label' => 'badge-warning' , 'categoria' => 20],
            ['id' => 92, 'name' => 'Cancelada', 'label' => 'badge-danger' , 'categoria' => 20],
            ['id' => 93, 'name' => 'Concluída', 'label' => 'badge-success' , 'categoria' => 20],
        ];

        $table->insert($rows)->saveData();
    }
}
