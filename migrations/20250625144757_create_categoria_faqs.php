<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoriaFaqs extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('categoria_faqs', [
            'id' => false,
            'primary_key' => ['id']
        ]);

        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->addColumn('nome', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => true
            ])
            ->create();

        $this->table('categoria_faqs')->insert([
            ['nome' => 'Cartão'],
            ['nome' => 'Recarga'],
            ['nome' => 'Documentação']
        ])->saveData();
    }
}
