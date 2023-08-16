<?php
use Phinx\Migration\AbstractMigration;

class AddsCSVImportToStatus extends AbstractMigration
{
    public function change()
    {
        $statuses = [
            [
                'name' => 'Sucesso',
                'label' => 'badge-success',
                'categoria' => 19,
            ],
            [
                'name' => 'Erro',
                'label' => 'badge-danger',
                'categoria' => 19,
            ],
            [
                'name' => 'Finalizado com erros',
                'label' => 'badge-warning',
                'categoria' => 19,
            ]
        ];

        $table = $this->table('statuses');
        foreach ($statuses as $status) {
            $table->insert($status);
        }
        $table->saveData();
    }
}
