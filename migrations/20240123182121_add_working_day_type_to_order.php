<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddWorkingDayTypeToOrder extends AbstractMigration
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
        $table = $this->table('orders');
        $table->addColumn('working_days_type', 'integer', [
            'default' => 1,
            'null' => true,
            'after' => 'economic_group_id',
        ])->addColumn('benefit_type', 'integer', [
            'default' => 0,
            'null' => true,
            'after' => 'working_days_type',
        ]);
        $table->update();
    }
}
