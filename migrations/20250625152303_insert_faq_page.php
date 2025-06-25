<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InsertFaqPage extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('pages');

        $rows = [
            ['id' => 81, 'name' => 'FAQ']
        ];

        $table->insert($rows)->saveData();
    }
}
