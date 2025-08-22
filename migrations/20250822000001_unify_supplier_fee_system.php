<?php

use Phinx\Migration\AbstractMigration;

class UnifySupplierFeeSystem extends AbstractMigration
{
    public function up()
    {
        // 1. Add new columns to supplier_volume_tiers table
        $this->execute("
            ALTER TABLE supplier_volume_tiers 
            ADD COLUMN fee_type ENUM('fixed', 'percentage') NOT NULL DEFAULT 'percentage' COMMENT 'Type of fee: fixed value or percentage' AFTER percentual_repasse,
            ADD COLUMN valor_fixo DECIMAL(10,2) NULL COMMENT 'Fixed fee value when fee_type=fixed' AFTER fee_type
        ");

        // 2. Set all suppliers to use 'pedido' billing type
        $this->execute("
            UPDATE suppliers 
            SET tipo_cobranca = 'pedido'
        ");

        // 3. Create volume tiers for existing Type 1 (Fixed Value) suppliers
        $this->execute("
            INSERT INTO supplier_volume_tiers (
                supplier_id, 
                de_qtd, 
                ate_qtd, 
                percentual_repasse, 
                fee_type, 
                valor_fixo,
                user_creator_id,
                created,
                data_cancel
            )
            SELECT 
                id as supplier_id,
                1 as de_qtd,
                999999 as ate_qtd,
                0.00 as percentual_repasse,
                'fixed' as fee_type,
                transfer_fee_percentage as valor_fixo,
                1 as user_creator_id,
                NOW() as created,
                '1901-01-01 00:00:00' as data_cancel
            FROM suppliers 
            WHERE transfer_fee_type = 1 
            AND transfer_fee_percentage IS NOT NULL
            AND transfer_fee_percentage > 0
            AND data_cancel = '1901-01-01 00:00:00'
        ");

        // 4. Create volume tiers for existing Type 2 (Percentage) suppliers
        $this->execute("
            INSERT INTO supplier_volume_tiers (
                supplier_id, 
                de_qtd, 
                ate_qtd, 
                percentual_repasse, 
                fee_type, 
                valor_fixo,
                user_creator_id,
                created,
                data_cancel
            )
            SELECT 
                id as supplier_id,
                1 as de_qtd,
                999999 as ate_qtd,
                transfer_fee_percentage as percentual_repasse,
                'percentage' as fee_type,
                NULL as valor_fixo,
                1 as user_creator_id,
                NOW() as created,
                '1901-01-01 00:00:00' as data_cancel
            FROM suppliers 
            WHERE transfer_fee_type = 2 
            AND transfer_fee_percentage IS NOT NULL
            AND transfer_fee_percentage > 0
            AND data_cancel = '1901-01-01 00:00:00'
        ");

        // 5. Update existing Type 3 suppliers to Type 2 (they already have volume tiers)
        $this->execute("
            UPDATE suppliers 
            SET transfer_fee_type = 2 
            WHERE transfer_fee_type = 3
            AND data_cancel = '1901-01-01 00:00:00'
        ");

        // 6. Update existing volume tiers to use percentage fee_type (default)
        $this->execute("
            UPDATE supplier_volume_tiers 
            SET fee_type = 'percentage' 
            WHERE fee_type IS NULL
        ");
    }

    public function down()
    {
        // Remove the new columns
        $this->execute("ALTER TABLE supplier_volume_tiers DROP COLUMN IF EXISTS valor_fixo");
        $this->execute("ALTER TABLE supplier_volume_tiers DROP COLUMN IF EXISTS fee_type");
        
        // Note: We don't restore the original transfer_fee_type values as this would be destructive
        // Manual intervention would be required to restore the original state
    }
}