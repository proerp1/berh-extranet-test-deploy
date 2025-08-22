<?php
/**
 * Classe utilitária para cálculos de repasse baseado em volume
 * 
 * Esta classe implementa a lógica de negócio para calcular valores de repasse
 * baseados nas faixas de volume configuradas para cada fornecedor.
 */
class RepaymentCalculator
{
    /**
     * Calcula o repasse para um fornecedor baseado na quantidade
     * 
     * @param int $supplierId ID do fornecedor
     * @param int $quantity Quantidade para calcular o repasse
     * @param float $baseValue Valor base sobre o qual aplicar o percentual
     * @return array Array com informações do cálculo
     */
    public static function calculateRepayment($supplierId, $quantity, $baseValue)
    {
        $supplierModel = ClassRegistry::init('Supplier');
        $supplier = $supplierModel->findById($supplierId);
        
        if (!$supplier) {
            throw new InvalidArgumentException("Fornecedor não encontrado: {$supplierId}");
        }
        
        $result = [
            'supplier_id' => $supplierId,
            'quantity' => $quantity,
            'base_value' => $baseValue,
            'repayment_type' => $supplier['Supplier']['transfer_fee_type'],
            'billing_type' => $supplier['Supplier']['tipo_cobranca'],
            'repayment_percentage' => 0,
            'repayment_value' => 0,
            'tier_used' => null,
            'calculation_method' => ''
        ];
        
        // All suppliers now use volume tier system
        $tierModel = ClassRegistry::init('SupplierVolumeTier');
        $tier = $tierModel->findTierForQuantity($supplierId, $quantity);
        
        if (!$tier) {
            throw new InvalidArgumentException("Nenhuma faixa de volume encontrada para quantidade {$quantity} do fornecedor {$supplierId}");
        }
        
        $result['tier_used'] = $tier['SupplierVolumeTier'];
        
        // Calculate based on tier fee type
        switch ($tier['SupplierVolumeTier']['fee_type']) {
            case 'fixed':
                $result['repayment_value'] = $tier['SupplierVolumeTier']['valor_fixo'];
                $result['calculation_method'] = 'volume_tier_fixed';
                break;
                
            case 'percentage':
                $result['repayment_percentage'] = isset($tier['SupplierVolumeTier']['percentual_repasse_nao_formatado']) 
                    ? $tier['SupplierVolumeTier']['percentual_repasse_nao_formatado']
                    : $tier['SupplierVolumeTier']['percentual_repasse'];
                $result['repayment_value'] = ($baseValue * $result['repayment_percentage']) / 100;
                $result['calculation_method'] = 'volume_tier_percentage';
                break;
                
            default:
                throw new InvalidArgumentException("Tipo de taxa inválido na faixa: {$tier['SupplierVolumeTier']['fee_type']}");
        }
        
        return $result;
    }
    
    /**
     * Calcula repasse consolidado por pedido
     * 
     * @param int $supplierId ID do fornecedor
     * @param array $orderItems Array de itens do pedido
     * @return array Resultado do cálculo
     */
    public static function calculateByOrder($supplierId, $orderItems)
    {
        $totalQuantity = 0;
        $totalValue = 0;
        
        foreach ($orderItems as $item) {
            $totalQuantity += $item['quantity'];
            $totalValue += $item['value'];
        }
        
        return self::calculateRepayment($supplierId, $totalQuantity, $totalValue);
    }
    
    /**
     * Calcula repasse consolidado por CPF
     * 
     * @param int $supplierId ID do fornecedor
     * @param string $cpf CPF do beneficiário
     * @param array $orderItems Array de itens para o CPF
     * @return array Resultado do cálculo
     */
    public static function calculateByCpf($supplierId, $cpf, $orderItems)
    {
        $totalQuantity = 0;
        $totalValue = 0;
        
        foreach ($orderItems as $item) {
            $totalQuantity += $item['quantity'];
            $totalValue += $item['value'];
        }
        
        $result = self::calculateRepayment($supplierId, $totalQuantity, $totalValue);
        $result['cpf'] = $cpf;
        $result['consolidation_type'] = 'by_cpf';
        
        return $result;
    }
    
    /**
     * Valida se um fornecedor está configurado corretamente para cálculo de repasse
     * 
     * @param int $supplierId ID do fornecedor
     * @return array Array com status da validação e mensagens
     */
    public static function validateSupplierConfiguration($supplierId)
    {
        $supplierModel = ClassRegistry::init('Supplier');
        $supplier = $supplierModel->findById($supplierId);
        
        $validation = [
            'is_valid' => true,
            'errors' => [],
            'warnings' => []
        ];
        
        if (!$supplier) {
            $validation['is_valid'] = false;
            $validation['errors'][] = "Fornecedor não encontrado";
            return $validation;
        }
        
        // Validar tipo de repasse
        if (empty($supplier['Supplier']['transfer_fee_type'])) {
            $validation['is_valid'] = false;
            $validation['errors'][] = "Tipo de repasse não configurado";
        }
        
        // Validar tipo de cobrança (agora obrigatório para todos)
        if (empty($supplier['Supplier']['tipo_cobranca'])) {
            $validation['is_valid'] = false;
            $validation['errors'][] = "Tipo de cobrança não configurado";
        }
        
        // Validar se existem faixas configuradas (agora obrigatório para todos)
        $tierModel = ClassRegistry::init('SupplierVolumeTier');
        $tiersCount = $tierModel->find('count', [
            'conditions' => [
                'SupplierVolumeTier.supplier_id' => $supplierId,
                'SupplierVolumeTier.data_cancel' => '1901-01-01 00:00:00'
            ]
        ]);
        
        if ($tiersCount == 0) {
            $validation['is_valid'] = false;
            $validation['errors'][] = "Nenhuma faixa de volume configurada";
        }
        
        // Verificar se há gaps nas faixas
        $tiers = $tierModel->find('all', [
            'conditions' => [
                'SupplierVolumeTier.supplier_id' => $supplierId,
                'SupplierVolumeTier.data_cancel' => '1901-01-01 00:00:00'
            ],
            'order' => ['SupplierVolumeTier.de_qtd' => 'ASC']
        ]);
        
        if (count($tiers) > 0 && $tiers[0]['SupplierVolumeTier']['de_qtd'] > 1) {
            $validation['warnings'][] = "A primeira faixa não começa em 1";
        }
        
        for ($i = 1; $i < count($tiers); $i++) {
            $previousTier = $tiers[$i-1]['SupplierVolumeTier'];
            $currentTier = $tiers[$i]['SupplierVolumeTier'];
            
            if ($currentTier['de_qtd'] != ($previousTier['ate_qtd'] + 1)) {
                $validation['warnings'][] = "Gap entre faixas: {$previousTier['ate_qtd']} e {$currentTier['de_qtd']}";
            }
        }
        
        // Validar configuração de cada faixa
        foreach ($tiers as $tier) {
            $tierData = $tier['SupplierVolumeTier'];
            
            if (empty($tierData['fee_type'])) {
                $validation['is_valid'] = false;
                $validation['errors'][] = "Tipo de taxa não configurado na faixa {$tierData['de_qtd']}-{$tierData['ate_qtd']}";
            } elseif ($tierData['fee_type'] == 'fixed') {
                if (empty($tierData['valor_fixo']) && $tierData['valor_fixo'] !== '0') {
                    $validation['is_valid'] = false;
                    $validation['errors'][] = "Valor fixo não configurado na faixa {$tierData['de_qtd']}-{$tierData['ate_qtd']}";
                }
            } elseif ($tierData['fee_type'] == 'percentage') {
                if (empty($tierData['percentual_repasse']) && $tierData['percentual_repasse'] !== '0') {
                    $validation['is_valid'] = false;
                    $validation['errors'][] = "Percentual não configurado na faixa {$tierData['de_qtd']}-{$tierData['ate_qtd']}";
                }
            }
        }
        
        return $validation;
    }
    
    /**
     * Gera relatório de simulação de repasse para diferentes quantidades
     * 
     * @param int $supplierId ID do fornecedor
     * @param array $quantities Array de quantidades para simular
     * @param float $baseValue Valor base para cálculo
     * @return array Relatório de simulação
     */
    public static function generateSimulationReport($supplierId, $quantities, $baseValue = 100.00)
    {
        $report = [
            'supplier_id' => $supplierId,
            'base_value' => $baseValue,
            'simulations' => []
        ];
        
        foreach ($quantities as $quantity) {
            try {
                $calculation = self::calculateRepayment($supplierId, $quantity, $baseValue);
                $report['simulations'][] = $calculation;
            } catch (Exception $e) {
                $report['simulations'][] = [
                    'quantity' => $quantity,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $report;
    }
}