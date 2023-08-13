<?php

use Phinx\Migration\AbstractMigration;

class CreateMaritalStatusesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('marital_statuses');
        $table->addColumn('status', 'string')
            ->addIndex('status', ['unique' => true])
            ->create();

        $statuses = [
            'Solteiro(a)',
            'Casado(a)',
            'Divorciado(a)',
            'Viúvo(a)',
            'Separado(a)',
            'União Estável'
        ];

        foreach ($statuses as $status) {
            $table->insert(['status' => $status]);
        }

        $table->saveData();
    }
}
