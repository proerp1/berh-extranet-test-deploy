<?php


use Phinx\Migration\AbstractMigration;

class CreateSituacaoDocumento extends AbstractMigration
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
            CREATE TABLE `situacaoDocumento` (
              `situacaoDocumentoID` INT(11) NOT NULL AUTO_INCREMENT,
              `situacaoDocumentoTipoDoc` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
              `situacaoDocumentoCodigo` INT(11) NULL DEFAULT NULL,
              `situacaoDocumentoDescricao` VARCHAR(50) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
              PRIMARY KEY (`situacaoDocumentoID`)
            )
            COLLATE='latin1_general_ci'
            ENGINE=InnoDB
            AUTO_INCREMENT=1
            ;
        ");

        $this->execute("
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (1, 'CPF', 2, 'Regular');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (2, 'CPF', 3, 'Pendente de Regularização');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (3, 'CPF', 6, 'Suspensa');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (4, 'CPF', 9, 'Cancelada');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (5, 'CPF', 4, 'Nula');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (6, 'CNPJ', 2, 'Ativa');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (7, 'CNPJ', 6, 'Suspensa');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (8, 'CNPJ', 0, 'Inapta');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (9, 'CNPJ', 7, 'Baixada');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (10, 'CNPJ', 4, 'Nula');
            INSERT INTO `situacaoDocumento` (`situacaoDocumentoID`, `situacaoDocumentoTipoDoc`, `situacaoDocumentoCodigo`, `situacaoDocumentoDescricao`) VALUES (11, 'CNPJ', 9, 'Cancelada');
        ");

    }
}
