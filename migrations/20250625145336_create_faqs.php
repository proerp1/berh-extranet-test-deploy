<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFaqs extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('faqs');

        $table
            ->addColumn('pergunta', 'text', ['null' => false])
            ->addColumn('resposta', 'text', ['null' => false])
            ->addColumn('user_creator_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => true
            ])
            ->addColumn('categoria_faq_id', 'integer', ['null' => true, 'default' => null, 'signed' => false])
            ->addColumn('sistema_destino', 'enum', [
                'values' => ['sig', 'cliente', 'todos'],
                'default' => 'todos',
                'null' => true
            ])
            ->addIndex('categoria_faq_id', ['name' => 'fk_faq_categoria'])
            ->addForeignKey('categoria_faq_id', 'categoria_faqs', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'RESTRICT',
                'constraint' => 'fk_faq_categoria'
            ])
            ->create();
    }
}
