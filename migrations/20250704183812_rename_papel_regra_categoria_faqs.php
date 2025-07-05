<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RenamePapelRegraCategoriaFaqs extends AbstractMigration
{
    public function up(): void
    {
        $this->execute("
            UPDATE categoria_faqs SET nome = 'Papel' WHERE id = 4;
        ");

        $this->execute("
            UPDATE categoria_faqs SET nome = 'Regra Operacional' WHERE id = 5;
        ");
    }

    public function down(): void
    {
        $this->execute("
            UPDATE categoria_faqs SET nome = 'papel' WHERE id = 4;
        ");

        $this->execute("
            UPDATE categoria_faqs SET nome = 'regra operacional' WHERE id = 5;
        ");
    }
}