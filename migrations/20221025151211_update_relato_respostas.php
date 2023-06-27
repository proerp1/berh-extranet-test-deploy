<?php

use Phinx\Migration\AbstractMigration;

class UpdateRelatoRespostas extends AbstractMigration
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
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=76;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=77;
            UPDATE `itensResposta` SET `itemRespostaNome`='Site', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=119;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=128;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=129;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=161;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=185;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=287;
            UPDATE `itensResposta` SET `itemRespostaNome`='Cliente consultante' WHERE  `itemRespostaID`=295;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=336;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=340;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=358;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=362;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=381;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=384;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=387;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=389;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=390;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=391;
        ");
    }
}
