<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMenusPermission extends AbstractMigration
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
        $this->execute("
            UPDATE pages 
            SET name = 'Compras - Movimentações' 
            WHERE id = 76
        ");

        $this->execute("
            UPDATE pages 
            SET name = 'Compras - Alteração Status Pedido' 
            WHERE id = 79
        ");

        $rows = [
            ['id' => 89, 'name' => 'Compras - Conversor de Compras'],
            ['id' => 90, 'name' => 'Logística - Conversor Logística'],
            ['id' => 91, 'name' => 'Compras - Liberação Técnica'],
        ];

        $this->table('pages')->insert($rows)->saveData();
    }
}
