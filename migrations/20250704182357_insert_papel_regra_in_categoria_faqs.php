<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InsertPapelRegraInCategoriaFaqs extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('categoria_faqs');

        $rows = [
            ['id' => 4, 'nome' => 'papel'],
            ['id' => 5, 'nome' => 'regra operacional']
        ];

        $table->insert($rows)->saveData();
    }
}
