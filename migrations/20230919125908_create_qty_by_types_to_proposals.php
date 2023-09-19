<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateQtyByTypesToProposals extends AbstractMigration
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
        $this->table('proposals')
                ->renameColumn('workers_qty', 'transport_workers_qty')
                ->renameColumn('workers_price', 'transport_workers_price')
                ->renameColumn('workers_price_total', 'transport_workers_price_total')
                
                ->addColumn('meal_workers_price_total', 'float', ['after' => 'meal_deli_fee'])
                ->addColumn('meal_workers_price', 'float', ['after' => 'meal_deli_fee'])
                ->addColumn('meal_workers_qty', 'integer', ['after' => 'meal_deli_fee'])
                
                ->addColumn('fuel_workers_price_total', 'float', ['after' => 'fuel_deli_fee'])
                ->addColumn('fuel_workers_price', 'float', ['after' => 'fuel_deli_fee'])
                ->addColumn('fuel_workers_qty', 'integer', ['after' => 'fuel_deli_fee'])
                
                ->addColumn('multi_card_workers_price_total', 'float', ['after' => 'multi_card_deli_fee'])
                ->addColumn('multi_card_workers_price', 'float', ['after' => 'multi_card_deli_fee'])
                ->addColumn('multi_card_workers_qty', 'integer', ['after' => 'multi_card_deli_fee'])
                
                ->addColumn('total_price', 'float')
                ->update();
    }
}
