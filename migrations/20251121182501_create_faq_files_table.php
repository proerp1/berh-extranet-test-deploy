<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFaqFilesTable extends AbstractMigration
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
        $table = $this->table('faq_files');

        $table
            ->addColumn('faq_id', 'integer', ['null' => true, 'default' => null, 'signed' => false])
            ->addColumn('file', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
                'after' => 'sistema_destino'
            ])->addColumn('file_dir', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
                'after' => 'file'
            ])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
            ->create();

        $table->update();
    }
}
