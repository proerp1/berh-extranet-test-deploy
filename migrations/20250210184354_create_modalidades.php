<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateModalidades extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     */
    public function change(): void
    {
        // Criar a tabela 'modalidades'
        $table = $this->table('modalidades');

        // Adicionar as colunas
        $table
              ->addColumn('status_id', 'integer', ['default' => 1])
              ->addColumn('name', 'string', ['limit' => 255])
              ->addColumn('description', 'text', ['null' => true])
              ->addColumn('created', 'datetime')
              ->addColumn('user_creator_id', 'integer', ['signed' => false])
              ->addColumn('updated', 'datetime', ['null' => true])
              ->addColumn('user_updated_id', 'integer', ['null' => true, 'signed' => false])
              ->addColumn('data_cancel', 'datetime', ['default' => '1901-01-01 00:00:00'])
              ->addColumn('usuario_id_cancel', 'integer', ['null' => true, 'signed' => false])
              ->addColumn('customer_id', 'integer', ['signed' => false]);

        // Criar as chaves primária e índices
        $table
              ->addIndex(['status_id'])
              ->addIndex(['customer_id'])
              ->create();
    }
}

