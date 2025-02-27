<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInclusaoItineraries extends AbstractMigration
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
        $table = $this->table('customer_user_itineraries');
        $table->addColumn('matricula', 'string', ['limit' => 100, 'null' => true, 'collation' => 'utf8_general_ci'])
        ->update();
    }
}
