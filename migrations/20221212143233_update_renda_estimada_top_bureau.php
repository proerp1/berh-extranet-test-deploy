<?php

use Phinx\Migration\AbstractMigration;

class UpdateRendaEstimadaTopBureau extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->execute("
            UPDATE `respostas` SET `respostaSubtipo`='RERD0' WHERE  `respostaID`=945;
            UPDATE `respostas` SET `respostaSubtipo`='RERD1' WHERE  `respostaID`=946;

            UPDATE `respostas` SET `respostaSubtipo`='BPRD0' WHERE  `respostaID`=940;
            UPDATE `respostas` SET `respostaSubtipo`='BPRD1' WHERE  `respostaID`=939;
        ");
    }
}
