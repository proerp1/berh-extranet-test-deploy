<?php


use Phinx\Migration\AbstractMigration;

class ImportBankTables extends AbstractMigration
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
        $this->execute("CREATE TABLE `banks` (
                            `id` INT(11) NOT NULL AUTO_INCREMENT,
                            `name` VARCHAR(255) NOT NULL,
                            `created` DATETIME NOT NULL,
                            `user_created` INT(11) NULL DEFAULT NULL,
                            `updated` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
                            `user_updated` INT(11) NULL DEFAULT NULL,
                            `data_cancel` DATETIME NULL DEFAULT '1901-01-01 00:00:00',
                            `usuario_id_cancel` INT(11) NULL DEFAULT NULL,
                            PRIMARY KEY (`id`)
                        )
                        COLLATE='utf8_general_ci'
                        ENGINE=InnoDB
                        AUTO_INCREMENT=1
                        ;");

        $bnk_acc = $this->table("bank_accounts");
        $bnk_acc->addColumn("bank_id", "integer", array("null" => "true", "default" => 5, "after" => "status_id"))
                ->update();
    }
}
