<?php


use Phinx\Migration\AbstractMigration;

class CreateClienteMeProteja extends AbstractMigration
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
            CREATE TABLE `clienteMeProteja` (
                `clienteMeProtejaID` INT(10) NOT NULL AUTO_INCREMENT,
                `clienteID` INT(11) NULL DEFAULT NULL,
                `billingID` INT(11) NULL DEFAULT NULL,
                `billingMonthlyPaymentID` INT(11) NULL DEFAULT NULL,
                `productID` INT(11) NULL DEFAULT NULL,
                `clienteMeProtejaValor` FLOAT NULL DEFAULT NULL,
                `clienteMeProtejaDias` INT(11) NULL DEFAULT NULL,
                `clienteMeProtejaValidade` DATETIME NULL DEFAULT NULL,
                `usuarioIDCadastro` INT(11) NULL DEFAULT NULL,
                `clienteMeProtejaIPCadastro` VARCHAR(250) NULL DEFAULT NULL,
                `clienteMeProtejaDataCadastro` DATETIME NULL DEFAULT NULL,
                `usuarioIDAlteracao` INT(11) NULL DEFAULT NULL,
                `clienteMeProtejaDataAlteracao` DATETIME NULL DEFAULT NULL,
                `usuarioIDCancel` INT(11) NULL DEFAULT NULL,
                `clienteMeProtejaDataCancel` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
                PRIMARY KEY (`clienteMeProtejaID`)
            )
            COLLATE='latin1_general_ci'
            ENGINE=MyISAM
            AUTO_INCREMENT=1
            ;
        ");
        

    }
}
