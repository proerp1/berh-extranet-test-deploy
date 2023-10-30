<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGerarNotaDebitoPermission extends AbstractMigration
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

        $this->execute('delete from pages where id in (66, 65, 45, 52, 53, 63, 64);');

        $rows = [
            ['id' => 66, 'name' => 'Pedidos - Gerar nota de débito'],
            ['id' => 65, 'name' => 'Beneficiários'],
            ['id' => 45, 'name' => 'Contas e Boletos'],
            ['id' => 52, 'name' => 'Financeiro - Boletos'],
            ['id' => 53, 'name' => 'Financeiro - Boletos em Lotes'],
            ['id' => 63, 'name' => 'Pedidos'],
            ['id' => 64, 'name' => 'Relatório Pedidos'],
        ];

        $this->table('pages')->insert($rows)->saveData();
    }
}
