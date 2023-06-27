<?php

use Phinx\Migration\AbstractMigration;

class AjustaEnderecosTelefonesBureau extends AbstractMigration
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
            UPDATE `respostas` SET `respostaNome`='Endereços' WHERE  `respostaID`=138;
            UPDATE `respostas` SET `respostaNome`='Melhor Endereço e Telefone', `respostaVisivelCliente`='1' WHERE  `respostaID`=137;
            UPDATE `respostas` SET `respostaVisivelCliente`='2' WHERE  `respostaID`=136;

            UPDATE `itensResposta` SET `itemRespostaNome`='Telefone residencial', `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1231;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1234;
            UPDATE `itensResposta` SET `itemRespostaByte`='8' WHERE  `itemRespostaID`=1235;
            UPDATE `itensResposta` SET `itemRespostaInicio`='105', `itemRespostaByte`='2' WHERE  `itemRespostaID`=1236;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1224;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1225;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=1226;
            UPDATE `itensResposta` SET `itemRespostaNome`='UF Nascimento' WHERE  `itemRespostaID`=1228;
        ");
    }
}
