<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateClienteGeLogs extends AbstractMigration
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
        $table = $this->table('customer_ge_logs');
        $table->addColumn('customer_id', 'integer', ['null' => false])
                ->addColumn('flag_gestao_economico', 'char', ['null' => true])
                ->addColumn('porcentagem_margem_seguranca', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
                ->addColumn('qtde_minina_diaria', 'integer', ['null' => true])
                ->addColumn('tipo_ge', 'integer', ['null' => true])
                ->addColumn('created', 'datetime')
                ->addColumn('user_creator_id', 'integer')
                ->addColumn('data_cancel', 'datetime', ['default' => '1901-01-01 00:00:00'])
                ->addColumn('usuario_id_cancel', 'integer', ['null' => true])
                ->create();
    }
}
