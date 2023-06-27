<?php

use Phinx\Migration\AbstractMigration;

class UpdatePendenciasConcentre extends AbstractMigration
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
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3156191;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3156189;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3156195;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3156070;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3156077;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3156076;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3156225;

            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3155689;
            UPDATE `itensResposta` SET `itemRespostaVisivelCliente`='1' WHERE  `itemRespostaID`=3155684;
        ");
    }
}
