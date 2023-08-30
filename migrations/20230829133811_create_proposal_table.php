<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProposalTable extends AbstractMigration
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
        $table = $this->table('proposals');
        $table->addColumn('number', 'string')
            ->addColumn('customer_id', 'integer')
            ->addColumn('date', 'date')
            ->addColumn('expected_closing_date', 'date')
            ->addColumn('closing_date', 'date')
            ->addColumn('workers_qty', 'integer')
            ->addColumn('workers_price', 'float')
            ->addColumn('workers_price_total', 'float')
            ->addColumn('transport_adm_fee', 'float')
            ->addColumn('transport_deli_fee', 'float')
            ->addColumn('management_feel', 'float')
            ->addColumn('meal_adm_fee', 'float')
            ->addColumn('meal_deli_fee', 'float')
            ->addColumn('fuel_adm_fee', 'float')
            ->addColumn('fuel_deli_fee', 'float')
            ->addColumn('multi_card_adm_fee', 'float')
            ->addColumn('multi_card_deli_fee', 'float')
            ->addColumn('created', 'datetime')
            ->addColumn('user_creator_id', 'integer')
            ->addColumn('updated', 'datetime', ['null' => true])
            ->addColumn('user_updated_id', 'integer', ['null' => true])
            ->addColumn('data_cancel', 'datetime', ['null' => true, 'default' => '1901-01-01 00:00:00'])
            ->addColumn('usuario_id_cancel', 'integer', ['null' => true])
            ->addIndex(['customer_id'])
            ->create();
    }
}
