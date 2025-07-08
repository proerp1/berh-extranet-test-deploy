<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFaqRelacionamentos extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('faq_relacionamentos', [
            'id' => false,
            'primary_key' => ['id']
        ]);

        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->addColumn('faq_id', 'integer', ['null' => true, 'signed' => false])
            ->addColumn('supplier_id', 'integer', ['null' => true, 'signed' => false])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => true
            ])
            ->addColumn('deleted', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['faq_id'], ['name' => 'idx_faq_id'])
            ->addIndex(['supplier_id'], ['name' => 'idx_supplier_id'])
            ->create();
    }
}
