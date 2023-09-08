<?php
use Phinx\Migration\AbstractMigration;

class ChangeComplementoColumnTypeInResales extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('resales');
        
        if ($table->hasColumn('complemento')) {
            $table->changeColumn('complemento', 'string', ['limit' => 255, 'null' => true])
                ->update();
        }
    }
}