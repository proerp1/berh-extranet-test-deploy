<?php


use Phinx\Migration\AbstractMigration;

class CreateApontamentoMeProteja extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->execute("
            CREATE TABLE `apontamentoMeProteja` (
                `apontamentoMeProtejaID` INT(10) NOT NULL AUTO_INCREMENT,
                `clienteID` INT(11) NULL DEFAULT NULL,
                `socioMeProtejaID` INT(11) NULL DEFAULT NULL,
                `situacaoDocumentoID` INT(11) NULL DEFAULT NULL,
                `cronMeProtejaID` INT(11) NULL DEFAULT NULL,
                `apontamentoMeProtejaEmail` INT(11) NULL DEFAULT NULL COMMENT '1 - Enviado;',
                `apontamentoMeProtejaTipo` INT(11) NULL DEFAULT NULL COMMENT '1 - Cliente; 2 - Socio;',
                `apontamentoMeProtejaQtde` INT(11) NULL DEFAULT NULL,
                `apontamentoMeProtejaString` LONGTEXT NULL COLLATE 'latin1_general_ci',
                `usuarioIDCadastro` INT(11) NULL DEFAULT NULL,
                `apontamentoMeProtejaDataCadastro` DATETIME NULL DEFAULT NULL,
                `usuarioIDAlteracao` INT(11) NULL DEFAULT NULL,
                `apontamentoMeProtejaDataAlteracao` DATETIME NULL DEFAULT NULL,
                `usuarioIDCancel` INT(11) NULL DEFAULT NULL,
                `apontamentoMeProtejaDataCancel` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
                PRIMARY KEY (`apontamentoMeProtejaID`)
            )
            COLLATE='latin1_general_ci'
            ENGINE=MyISAM
            AUTO_INCREMENT=1
            ;
        ");
    }
}
