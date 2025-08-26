<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSupplierLogTable extends AbstractMigration
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
      $table = $this->table('log_suppliers');
      $table->addColumn('supplier_id', 'integer')
        ->addColumn('transfer_fee_type', 'integer')
        ->addColumn('realiza_gestao_eficiente', 'integer')
        ->addColumn('modalidade_id', 'integer')
        ->addColumn('tecnologia_id', 'integer')
        ->addColumn('versao_credito_id', 'integer')
        ->addColumn('versao_cadastro_id', 'integer')
        ->addColumn('account_type_id', 'integer')
        ->addColumn('bank_code_id', 'integer')
        ->addColumn('payment_method', 'integer')
        ->addColumn('branch_number', 'integer')
        ->addColumn('branch_digit', 'integer')
        ->addColumn('acc_number', 'integer')
        ->addColumn('acc_digit', 'integer')
        ->addColumn('pix_type', 'string')
        ->addColumn('pix_id', 'string')

        ->addColumn('created', 'datetime')
        ->addColumn('user_creator_id', 'integer')
        ->addColumn('updated', 'datetime', ['null' => true])
        ->addColumn('user_updated_id', 'integer', ['null' => true])
        ->addColumn('data_cancel', 'datetime', ['null' => true, 'default' => '1901-01-01 00:00:00'])
        ->addColumn('usuario_id_cancel', 'integer', ['null' => true])
        ->create();
    }
}
