<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEconomicGroupProposals extends AbstractMigration
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
        $table = $this->table('economic_group_proposals');
        $table->addColumn('status_id', 'integer', ['null' => false, 'default' => 0, 'limit' => 11])
            ->addColumn('customer_id', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('economic_group_id', 'integer', ['null' => true, 'limit' => 11])        
            ->addColumn('number', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('date', 'date', ['null' => true])
            ->addColumn('expected_closing_date', 'date', ['null' => true])
            ->addColumn('closing_date', 'date', ['null' => true])
            ->addColumn('transport_workers_qty', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('transport_workers_price', 'float', ['null' => true])
            ->addColumn('transport_workers_price_total', 'float', ['null' => true])
            ->addColumn('transport_adm_fee', 'float', ['null' => true])
            ->addColumn('transport_deli_fee', 'float', ['null' => true])
            ->addColumn('management_feel', 'float', ['null' => true])
            ->addColumn('meal_adm_fee', 'float', ['null' => true])
            ->addColumn('meal_deli_fee', 'float', ['null' => true])
            ->addColumn('meal_workers_qty', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('meal_workers_price', 'float', ['null' => true])
            ->addColumn('meal_workers_price_total', 'float', ['null' => true])
            ->addColumn('fuel_adm_fee', 'float', ['null' => true])
            ->addColumn('fuel_deli_fee', 'float', ['null' => true])
            ->addColumn('fuel_workers_qty', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('fuel_workers_price', 'float', ['null' => true])
            ->addColumn('fuel_workers_price_total', 'float', ['null' => true])
            ->addColumn('multi_card_adm_fee', 'float', ['null' => true])
            ->addColumn('multi_card_deli_fee', 'float', ['null' => true])
            ->addColumn('multi_card_workers_qty', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('multi_card_workers_price', 'float', ['null' => true])
            ->addColumn('multi_card_workers_price_total', 'float', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('user_creator_id', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('updated', 'datetime', ['null' => true])
            ->addColumn('user_updated_id', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('data_cancel', 'datetime', ['null' => true,'default' => '1901-01-01 00:00:00'])
            ->addColumn('usuario_id_cancel', 'integer', ['null' => true, 'limit' => 11])
            ->addColumn('total_price', 'float', ['null' => true])
            ->addColumn('saude_card_adm_fee', 'float', ['null' => true])
            ->addColumn('saude_card_deli_fee', 'float', ['null' => true])
            ->addColumn('saude_card_workers_qty', 'float', ['null' => true])
            ->addColumn('saude_card_workers_price', 'float', ['null' => true])
            ->addColumn('saude_card_workers_price_total', 'float', ['null' => true])
            ->addColumn('prev_card_adm_fee', 'float', ['null' => true])
            ->addColumn('prev_card_deli_fee', 'float', ['null' => true])
            ->addColumn('prev_card_workers_qty', 'float', ['null' => true])
            ->addColumn('prev_card_workers_price', 'float', ['null' => true])
            ->addColumn('prev_card_workers_price_total', 'float', ['null' => true])
            ->addColumn('tpp', 'float', ['null' => true])
            ->addColumn('cancelled_description', 'text', ['null' => true])
            ->addIndex(['customer_id'])
            ->addIndex(['economic_group_id'])
            ->addIndex(['status_id'])
            ->create();
    }
}