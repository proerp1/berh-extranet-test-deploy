<?php

use Phinx\Migration\AbstractMigration;

class CreateSalaryRangesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('salary_ranges', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'integer', ['identity' => true])
              ->addColumn('range', 'string', ['limit' => 255])
              ->addColumn('min_salary', 'decimal', ['precision' => 10, 'scale' => 2])
              ->addColumn('max_salary', 'decimal', ['precision' => 10, 'scale' => 2])
              ->create();

        // Generate inserts for salary ranges from R$1,000 to R$10,000+
        $data = [];
        $minSalary = 1001;
        $data[] = [
            'range' => 'Até R$ 1.000,00',
            'min_salary' => 0,
            'max_salary' => 1000,
        ];
        for ($i = 2; $i <= 10; $i++) {
            $maxSalary = $minSalary + 1000 - 1;
            $data[] = [
                'range' => 'R$' . number_format($minSalary, 2) . ' - R$' . number_format($maxSalary, 2),
                'min_salary' => $minSalary,
                'max_salary' => $maxSalary,
            ];
            $minSalary = $maxSalary + 1; // Move to the next range
        }

        $data[] = [
            'range' => '+ R$ 10.001,00',
            'min_salary' => 10001,
            'max_salary' => 1000000,
        ];

        $this->table('salary_ranges')->insert($data)->save();
    }
}
