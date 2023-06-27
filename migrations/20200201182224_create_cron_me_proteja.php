<?php


use Phinx\Migration\AbstractMigration;

class CreateCronMeProteja extends AbstractMigration
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
            CREATE TABLE `cronMeProteja` (
                `cronMeProtejaID` INT(10) NOT NULL AUTO_INCREMENT,
                `clienteID` INT(11) NULL DEFAULT NULL,
                `cronMeProtejaValidade` DATETIME NULL DEFAULT NULL,
                `cronMeProtejaApontamento` DATETIME NULL DEFAULT NULL,
                `usuarioIDCadastro` INT(11) NULL DEFAULT NULL,
                `cronMeProtejaDataCadastro` DATETIME NULL DEFAULT NULL,
                `usuarioIDAlteracao` INT(11) NULL DEFAULT NULL,
                `cronMeProtejaDataAlteracao` DATETIME NULL DEFAULT NULL,
                `usuarioIDCancel` INT(11) NULL DEFAULT NULL,
                `cronMeProtejaDataCancel` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
                PRIMARY KEY (`cronMeProtejaID`)
            )
            COLLATE='latin1_general_ci'
            ENGINE=MyISAM
            AUTO_INCREMENT=1
            ;
        ");

    }
}
