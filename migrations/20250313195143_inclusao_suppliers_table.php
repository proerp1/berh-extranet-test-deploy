<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InclusaoSuppliersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     */
    public function change(): void
    {
        $table = $this->table('suppliers');
        $table->addColumn('registro_cobranca', 'integer', ['null' => true])
              ->addColumn('valor', 'integer', ['null' => true])
              ->addColumn('unidade_tempo', 'enum', ['values' => ['hrs', 'dias'], 'null' => true])
              ->update();
    }
}
