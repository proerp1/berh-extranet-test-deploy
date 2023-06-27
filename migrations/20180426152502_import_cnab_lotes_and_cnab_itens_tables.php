<?php


use Phinx\Migration\AbstractMigration;

class ImportCnabLotesAndCnabItensTables extends AbstractMigration
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
        $this->execute("CREATE TABLE `cnab_items` (
                            `id` INT(11) NOT NULL AUTO_INCREMENT,
                            `status_id` INT(11) NOT NULL,
                            `cnab_lote_id` INT(11) NOT NULL,
                            `income_id` INT(11) NOT NULL,
                            `created` DATETIME NOT NULL,
                            `user_creator_id` INT(11) NOT NULL,
                            `updated` DATETIME NULL DEFAULT NULL,
                            `user_updated_id` INT(11) NULL DEFAULT NULL,
                            `data_cancel` DATETIME NOT NULL DEFAULT '1901-01-01 00:00:00',
                            `usuario_id_cancel` INT(11) NULL DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            INDEX `status_id` (`status_id`, `cnab_lote_id`, `income_id`, `user_updated_id`, `usuario_id_cancel`, `user_creator_id`)
                        )
                        COLLATE='utf8_general_ci'
                        ENGINE=InnoDB
                        AUTO_INCREMENT=1
                        ;");

        $this->execute("CREATE TABLE `cnab_lotes` (
                            `id` INT(11) NOT NULL AUTO_INCREMENT,
                            `status_id` INT(11) NOT NULL,
                            `bank_id` INT(11) NOT NULL,
                            `remessa` INT(11) NOT NULL,
                            `arquivo` VARCHAR(255) NOT NULL,
                            `created` DATETIME NOT NULL,
                            `user_creator_id` INT(11) NOT NULL,
                            `updated` DATETIME NULL DEFAULT NULL,
                            `user_updated_id` INT(11) NULL DEFAULT NULL,
                            `data_cancel` DATETIME NOT NULL DEFAULT '1901-01-01 00:00:00',
                            `usuario_id_cancel` INT(11) NULL DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            INDEX `status_id` (`status_id`, `user_updated_id`, `usuario_id_cancel`, `user_creator_id`),
                            INDEX `bank_id` (`bank_id`)
                        )
                        COLLATE='utf8_general_ci'
                        ENGINE=InnoDB
                        AUTO_INCREMENT=1
                        ;");
    }
}
