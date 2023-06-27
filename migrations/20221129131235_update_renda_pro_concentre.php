<?php

use Phinx\Migration\AbstractMigration;

class UpdateRendaProConcentre extends AbstractMigration
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
            UPDATE `itensResposta` SET `itemRespostaNome`='Código da Feature - RSRD' WHERE  `itemRespostaID`=3157629;
            UPDATE `itensResposta` SET `itemRespostaNome`='Código da feature solicitada - RSRD' WHERE  `itemRespostaID`=3157614;
            UPDATE `respostas` SET `respostaSubtipo`='RSRD1' WHERE  `respostaID`=952;
            UPDATE `respostas` SET `respostaSubtipo`='RSRD0' WHERE  `respostaID`=951;
        ");
    }
}
