<?php


use Phinx\Migration\AbstractMigration;

class ImportBankTicketsTable extends AbstractMigration
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
        $this->execute("CREATE TABLE `bank_tickets` (
                            `id` INT(11) NOT NULL AUTO_INCREMENT,
                            `status_id` INT(11) NOT NULL,
                            `bank_account_id` INT(11) NOT NULL,
                            `carteira` VARCHAR(255) NOT NULL,
                            `codigo_cedente` VARCHAR(255) NOT NULL,
                            `cobranca_taxa_bancaria` VARCHAR(255) NOT NULL,
                            `valor_taxa_bancaria` DECIMAL(10,2) NULL DEFAULT NULL,
                            `multa_boleto` DECIMAL(10,2) NULL DEFAULT NULL,
                            `juros_boleto_dia` DECIMAL(10,2) NULL DEFAULT NULL,
                            `instrucao_boleto_1` VARCHAR(255) NULL DEFAULT NULL,
                            `instrucao_boleto_2` VARCHAR(255) NULL DEFAULT NULL,
                            `instrucao_boleto_3` VARCHAR(255) NULL DEFAULT NULL,
                            `instrucao_boleto_4` VARCHAR(255) NULL DEFAULT NULL,
                            `cabecalho_boleto_1` VARCHAR(255) NULL DEFAULT NULL,
                            `cabecalho_boleto_2` VARCHAR(255) NULL DEFAULT NULL,
                            `cabecalho_boleto_3` VARCHAR(255) NULL DEFAULT NULL,
                            `informativo_boleto` TEXT NULL,
                            `observacao` TEXT NULL,
                            `created` DATETIME NULL DEFAULT NULL,
                            `user_creator_id` INT(11) NOT NULL,
                            `data_cancel` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
                            `usuario_id_cancel` INT(11) NOT NULL,
                            `user_updated_id` INT(11) NOT NULL,
                            `updated` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
                            PRIMARY KEY (`id`)
                        )
                        COLLATE='utf8_general_ci'
                        ENGINE=InnoDB
                        AUTO_INCREMENT=1
                        ;");
    }
}
