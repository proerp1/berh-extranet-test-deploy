<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColunasCustomer extends AbstractMigration
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
        $table->addColumn('enderecoentrega', 'string', ['limit' => 45, 'null' => true, 'collation' => 'utf8_general_ci'])
              ->addColumn('numeroentrega', 'integer', ['null' => true])
              ->addColumn('complementoentrega', 'string', ['limit' => 45, 'null' => true, 'collation' => 'utf8_general_ci'])
              ->addColumn('bairroentrega', 'string', ['limit' => 20, 'null' => true, 'collation' => 'utf8_general_ci'])
              ->addColumn('cepentrega', 'string', ['limit' => 11, 'null' => true, 'collation' => 'utf8_general_ci'])
              ->addColumn('cidadeentrega', 'string', ['limit' => 25, 'null' => true, 'collation' => 'utf8_general_ci'])
              ->addColumn('estadoentrega', 'string', ['limit' => 2, 'null' => true, 'collation' => 'utf8_general_ci'])
              ->update();
    }
    
}
