<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSupplierGestaoEficiente extends AbstractMigration
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
        // Adicionando a coluna realiza_gestao_eficiente na tabela supplier
        $table = $this->table('suppliers');
        $table->addColumn('realiza_gestao_eficiente', 'boolean', ['default' => 0,'null' => false,'comment' => 'Indica se realiza gestão eficiente (Sim ou Não)']);
        $table->update();
    }
}
