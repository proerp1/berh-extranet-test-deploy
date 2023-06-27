<?php

use Phinx\Migration\AbstractMigration;

class UpdateRechequeRespostas extends AbstractMigration
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
            UPDATE `itensResposta` SET `itemRespostaNome`='Nome' WHERE  `itemRespostaID`=2330;
            UPDATE `itensResposta` SET `itemRespostaNome`='Data de nasc' WHERE  `itemRespostaID`=2331;
            UPDATE `itensResposta` SET `itemRespostaNome`='Última atualização' WHERE  `itemRespostaID`=2333;
            UPDATE `itensResposta` SET `itemRespostaNome`='Situação do doc' WHERE  `itemRespostaID`=2332;
            UPDATE `respostas` SET `respostaNome`='Recheque' WHERE  `respostaID`=284;
            UPDATE `respostas` SET `respostaNome`='Recheque' WHERE  `respostaID`=285;
            UPDATE `respostas` SET `respostaNome`='Histórico de ocorrências com cheques', `respostaVisivelCliente`='1' WHERE  `respostaID`=286;
            UPDATE `respostas` SET `respostaNome`='Histórico de ocorrências com cheques' WHERE  `respostaID`=288;
            UPDATE `respostas` SET `respostaNome`='Totais de Histórico de ocorrências com cheques' WHERE  `respostaID`=287;
            UPDATE `respostas` SET `respostaVisivelCliente`='1' WHERE  `respostaID`=287;
            UPDATE `respostas` SET `respostaVisivelCliente`='1' WHERE  `respostaID`=278;
            UPDATE `respostas` SET `respostaVisivelCliente`='1' WHERE  `respostaID`=335;

            UPDATE `itensResposta` SET `itemRespostaNome`='Data' WHERE  `itemRespostaID`=2455;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=2456;
            UPDATE `itensResposta` SET `itemRespostaNome`='Alínea', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=2457;
            UPDATE `itensResposta` SET `itemRespostaNome`='Valor', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=2459;
        ");

    }
}
