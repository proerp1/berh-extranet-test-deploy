<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColunasSuppliers extends AbstractMigration
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
        $table = $this->table('suppliers');
        $table->addColumn('observacao', 'text', ['null' => true,'collation' => 'utf8_general_ci','encoding' => 'utf8'])
              ->addColumn('enderecofaturamento', 'string', ['limit' => 255, 'null' => false, 'default' => '1'])
              ->addColumn('numerofaturamento', 'integer', ['limit' => 11, 'null' => true])
              ->update();
    }
    
}
