<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAddColumnsSuppliers  extends AbstractMigration
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
        // Adicionar as colunas à tabela 'suppliers'
        $table = $this->table('suppliers');
        
        // Adicionando as colunas com os valores especificados
        $table->addColumn('modalidade_id', 'integer', ['null' => true, 'default' => null])
              ->addColumn('tecnologia_id', 'integer', ['null' => true, 'default' => null])
              ->addColumn('regioes', 'integer', ['null' => true, 'default' => null])
              ->update();
    }
}
