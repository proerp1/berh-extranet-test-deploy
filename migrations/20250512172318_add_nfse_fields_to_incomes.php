<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddNfseFieldsToIncomes extends AbstractMigration
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
            ['id' => 105, 'name' => 'Não enviado', 'label' => 'badge-secondary', 'categoria' => '22'],
            ['id' => 106, 'name' => 'Em Processamento', 'label' => 'badge-warning', 'categoria' => '22'],
            ['id' => 107, 'name' => 'Emitido', 'label' => 'badge-success', 'categoria' => '22'],
            ['id' => 108, 'name' => 'Cancelado', 'label' => 'badge-danger', 'categoria' => '22'],
        ];

        $this->table('statuses')->insert($rows)->saveData();

        $table = $this->table('incomes');
        $table->addColumn('nfse_chave', 'string', ['null' => true])
            ->addColumn('nfse_status_id', 'integer', ['default' => 105])
            ->update();
    }
}
