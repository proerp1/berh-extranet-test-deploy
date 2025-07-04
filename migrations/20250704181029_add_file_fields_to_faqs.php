<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddFileFieldsToFaqs extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('faqs');

        $table->addColumn('file', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
            'after' => 'sistema_destino'
        ]);

        $table->addColumn('file_dir', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
            'after' => 'file'
        ]);

        $table->update();
    }
}