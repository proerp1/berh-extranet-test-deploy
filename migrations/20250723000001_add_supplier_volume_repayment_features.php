<?php

use Phinx\Migration\AbstractMigration;

class AddSupplierVolumeRepaymentFeatures extends AbstractMigration
{
    public function up()
    {
        // Add new field to suppliers table
        $this->execute("ALTER TABLE suppliers ADD COLUMN tipo_cobranca ENUM('pedido', 'cpf') NULL COMMENT 'Tipo de cobrança: por pedido ou por CPF' AFTER versao_cadastro_id");

        // Create supplier_volume_tiers table
        $this->execute("
            CREATE TABLE supplier_volume_tiers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                supplier_id INT NOT NULL,
                de_qtd INT NOT NULL COMMENT 'Quantidade inicial da faixa',
                ate_qtd INT NOT NULL COMMENT 'Quantidade final da faixa',
                percentual_repasse DECIMAL(5,2) NOT NULL COMMENT 'Percentual de repasse para esta faixa',
                created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                user_creator_id INT NOT NULL,
                updated DATETIME NULL,
                user_updated_id INT NULL,
                data_cancel DATETIME DEFAULT '1901-01-01 00:00:00' NOT NULL,
                usuario_id_cancel INT NULL,
                INDEX idx_supplier_id (supplier_id),
                INDEX idx_supplier_cancel (supplier_id, data_cancel),
                FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC
        ");
    }

    public function down()
    {
        // Drop the volume tiers table
        $this->execute("DROP TABLE IF EXISTS supplier_volume_tiers");
        
        // Remove the new column from suppliers table
        $this->execute("ALTER TABLE suppliers DROP COLUMN IF EXISTS tipo_cobranca");
    }
}