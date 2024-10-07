<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTipoGEToCustomers extends AbstractMigration
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
        $table = $this->table('customers');
        $table->addColumn('flag_gestao_economico', 'char', ['null' => true, 'default' => 'N'])
                ->addColumn('porcentagem_margem_seguranca', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true, 'default' => '100'])
                ->addColumn('qtde_minina_diaria', 'integer', ['null' => true, 'default' => '0'])
                ->addColumn('tipo_ge', 'integer', ['null' => true, 'default' => '1'])
                ->update();
    }
}
