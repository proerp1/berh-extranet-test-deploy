<?php


use Phinx\Migration\AbstractMigration;

class CreateSocioMeProteja extends AbstractMigration
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
            CREATE TABLE `sociosMeProteja` (
              `socioMeProtejaID` INT(10) NOT NULL AUTO_INCREMENT,
              `clienteID` INT(11) NULL DEFAULT NULL,
              `situacaoDocumentoID` INT(11) NULL DEFAULT NULL,
              `socioMeProtejaNome` VARCHAR(250) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
              `socioMeProtejaEmail` VARCHAR(250) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
              `socioMeProtejaCelular` VARCHAR(250) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
              `socioMeProtejaTipoDoc` INT(11) NULL DEFAULT NULL COMMENT '1 - CPF; 2 - CNPJ;',
              `socioMeProtejaDoc` VARCHAR(250) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
              `socioMeProtejaApontamento` DATETIME NULL DEFAULT NULL,
              `usuarioIDCadastro` INT(11) NULL DEFAULT NULL,
              `socioMeProtejaDataCadastro` DATETIME NULL DEFAULT NULL,
              `usuarioIDAlteracao` INT(11) NULL DEFAULT NULL,
              `socioMeProtejaDataAlteracao` DATETIME NULL DEFAULT NULL,
              `usuarioIDCancel` INT(11) NULL DEFAULT NULL,
              `socioMeProtejaDataCancel` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
              PRIMARY KEY (`socioMeProtejaID`)
            )
            COLLATE='latin1_general_ci'
            ENGINE=MyISAM
            AUTO_INCREMENT=1
            ;
        ");
    }
}
