<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCustomerUserItineraryLogsTable extends AbstractMigration
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
        $table = $this->table('customer_user_itinerary_logs');
        $table->addColumn('customer_user_id', 'integer', ['null' => false])
              ->addColumn('benefit_id', 'integer', ['null' => false])
              ->addColumn('customer_user_itinerary_id', 'integer', ['null' => true])
              ->addColumn('action', 'string', ['limit' => 50, 'null' => false])
              ->addColumn('user_id', 'integer', ['null' => false])
              ->addColumn('user_name', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('before_data', 'text', ['null' => true])
              ->addColumn('after_data', 'text', ['null' => true])
              ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['customer_user_id'])
              ->addIndex(['benefit_id'])
              ->addIndex(['customer_user_itinerary_id'])
              ->addIndex(['user_id'])
              ->addIndex(['created_at'])
              ->create();
    }
}
