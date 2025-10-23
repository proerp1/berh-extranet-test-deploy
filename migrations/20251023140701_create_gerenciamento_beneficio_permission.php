<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGerenciamentoBeneficioPermission extends AbstractMigration
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
        // Insert the new page permission
        $rows = [
            ['id' => 95, 'name' => 'Página Gerenciamento Beneficiário/Benefício'],
        ];

        $this->table('pages')->insert($rows)->saveData();

        // Grant permission to Group 1 (Admin)
        $permissionRows = [
            [
                'page_id' => 95,
                'group_id' => 1,
                'leitura' => 1,
                'escrita' => 1,
                'excluir' => 1
            ],
        ];

        $this->table('permissions')->insert($permissionRows)->saveData();
    }
}
