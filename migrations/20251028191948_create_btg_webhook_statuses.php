<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBtgWebhookStatuses extends AbstractMigration
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
        $rows = [
            ['id' => 117, 'name' => 'Pagamento confirmado', 'label' => 'badge-success', 'categoria' => '23'],
            ['id' => 118, 'name' => 'Pagamento criado', 'label' => 'badge-success', 'categoria' => '23'],
            ['id' => 119, 'name' => 'Pagamento cancelado', 'label' => 'badge-danger', 'categoria' => '23'],
            ['id' => 120, 'name' => 'Falha no pagamento', 'label' => 'badge-danger', 'categoria' => '23'],
            ['id' => 121, 'name' => 'Pagamento processado', 'label' => 'badge-success', 'categoria' => '23'],
            ['id' => 122, 'name' => 'Pagamento adiado', 'label' => 'badge-warning', 'categoria' => '23'],
            ['id' => 123, 'name' => 'Pagamento estornado', 'label' => 'badge-warning', 'categoria' => '23'],
            ['id' => 124, 'name' => 'Pagamento agendado', 'label' => 'badge-success', 'categoria' => '23'],
            ['id' => 125, 'name' => 'Pagamento validado', 'label' => 'badge-success', 'categoria' => '23'],
            ['id' => 126, 'name' => 'Pagamento invalidado', 'label' => 'badge-danger', 'categoria' => '23']
        ];

        $this->table('statuses')->insert($rows)->saveData();
    }
}
