<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFinanceiroPermission extends AbstractMigration
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
        $this->execute("
            UPDATE pages 
            SET name = 'Financeiro - Configurações - Contas e Boletos' 
            WHERE id = 12
        ");

        $this->execute("
            UPDATE pages 
            SET name = 'Financeiro - Relatórios - Fluxo de caixa' 
            WHERE id = 30
        ");

        $this->execute("
            UPDATE pages 
            SET name = 'Financeiro - Relatórios - Despesas' 
            WHERE id = 31
        ");

        $this->execute("
            UPDATE pages 
            SET name = 'Financeiro - Boletos - Emitir Boletos' 
            WHERE id = 52
        ");

        $this->execute("
            UPDATE pages 
            SET name = 'Financeiro - Boletos - Lotes Boleto' 
            WHERE id = 53
        ");

        $this->execute("
            UPDATE pages 
            SET name = 'Financeiro - Relatórios - Notas Fiscais Emitidas' 
            WHERE id = 69
        ");

        $rows = [
            ['id' => 42, 'name' => 'Financeiro - Configurações - Plano de Contas'],
            ['id' => 92, 'name' => 'Financeiro - Relatórios - Baixa manual'],
        ];

        $this->table('pages')->insert($rows)->saveData();
    }
}
