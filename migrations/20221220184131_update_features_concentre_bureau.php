<?php

use Phinx\Migration\AbstractMigration;

class UpdateFeaturesConcentreBureau extends AbstractMigration
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
            UPDATE `respostas` SET `respostaVisivelCliente`='1' WHERE  `respostaID`=401;

            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3345;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='2' WHERE  `itemRespostaID`=3340;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3342;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3346;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3350;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3344;

            UPDATE `itensResposta` SET `itemRespostaByte`='04' WHERE  `itemRespostaID`=3157255;
            UPDATE `itensResposta` SET `itemRespostaNome`='Código da Feature' WHERE  `itemRespostaID`=3157255;
            UPDATE `itensResposta` SET `itemRespostaInicio`='11', `itemRespostaByte`='02', `itemRespostaNome`='Faixa' WHERE  `itemRespostaID`=3157256;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='2' WHERE  `itemRespostaID`=3157256;
            UPDATE `itensResposta` SET `itemRespostaInicio`='92', `itemRespostaByte`='24' WHERE  `itemRespostaID`=3157257;

            INSERT INTO `itensResposta` (`respostaID`, `itemRespostaInicio`, `itemRespostaByte`, `itemRespostaNome`, `itemRespostaObservacao`, `itemRespostaDataCancel`, `usuarioIDCancel`, `itemRespostaVisivelCliente`, `itemRespostaMultivalorado`, `itemRespostaFormatacao`, `itemRespostaMsgPersonalizada`) VALUES (898, '13', '01', 'Calculado', NULL, '1901-01-01 00:00:00', NULL, 2, 1, 0, '');
            INSERT INTO `itensResposta` (`respostaID`, `itemRespostaInicio`, `itemRespostaByte`, `itemRespostaNome`, `itemRespostaObservacao`, `itemRespostaDataCancel`, `usuarioIDCancel`, `itemRespostaVisivelCliente`, `itemRespostaMultivalorado`, `itemRespostaFormatacao`, `itemRespostaMsgPersonalizada`) VALUES (898, '14', '78', 'Mensagem', NULL, '1901-01-01 00:00:00', NULL, 1, 1, 0, NULL);

        ");
    }
}
