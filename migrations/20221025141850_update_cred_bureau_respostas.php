<?php

use Phinx\Migration\AbstractMigration;

class UpdateCredBureauRespostas extends AbstractMigration
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
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1201;
            UPDATE `itensResposta` SET `itemRespostaNome`='Última atualização do CPF' WHERE  `itemRespostaID`=1191;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1887;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1895;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1898;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1868;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1867;
            UPDATE `itensResposta` SET `itemRespostaNome`='Avalista', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1862;
            UPDATE `itensResposta` SET `itemRespostaNome`='RG', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1203;
            UPDATE `itensResposta` SET `itemRespostaNome`='Data de emissão', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1205;
            UPDATE `itensResposta` SET `itemRespostaNome`='Órgão', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1204;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1206;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1198;
        ");
    }
}
