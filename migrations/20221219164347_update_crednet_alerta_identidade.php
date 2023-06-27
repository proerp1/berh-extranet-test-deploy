<?php

use Phinx\Migration\AbstractMigration;

class UpdateCrednetAlertaIdentidade extends AbstractMigration
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
            UPDATE `respostas` SET `respostaSubtipo`='REPG0' WHERE  `respostaID`=947;
            UPDATE `respostas` SET `respostaSubtipo`='REPG1' WHERE  `respostaID`=948;
            UPDATE `respostas` SET `respostaSubtipo`='01' WHERE  `respostaID`=555;

            UPDATE `itensResposta` SET `itemRespostaByte`='2' WHERE  `itemRespostaID`=3155164;
            UPDATE `itensResposta` SET `itemRespostaInicio`='9' WHERE  `itemRespostaID`=3155165;
            UPDATE `itensResposta` SET `itemRespostaInicio`='109', `itemRespostaByte`='7' WHERE  `itemRespostaID`=3155166;
        ");
    }
}
