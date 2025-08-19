<?php

class OrderItemCalculationAudit extends AppModel
{
    public $name = 'OrderItemCalculationAudit';
    public $useTable = 'order_item_calculation_audit';
    public $primaryKey = 'id';

    public $belongsTo = [
        'OrderItem' => [
            'className' => 'OrderItem',
            'foreignKey' => 'order_item_id'
        ],
        'Supplier' => [
            'className' => 'Supplier',
            'foreignKey' => 'supplier_id'
        ],
        'SupplierVolumeTier' => [
            'className' => 'SupplierVolumeTier',
            'foreignKey' => 'tier_id'
        ]
    ];

    /**
     * Create audit record for a calculation
     */
    public static function auditCalculation($orderItemId, $supplierId, $calculationData)
    {
        $model = ClassRegistry::init('OrderItemCalculationAudit');
        
        $auditData = [
            'OrderItemCalculationAudit' => [
                'order_item_id' => $orderItemId,
                'supplier_id' => $supplierId,
                'calculation_type' => $calculationData['calculation_method'],
                'base_value' => $calculationData['base_value'],
                'calculated_fee' => $calculationData['calculated_fee'],
                'calculation_version' => '1.0'
            ]
        ];

        // Add volume tier specific data
        if ($calculationData['calculation_method'] == 'volume_tier') {
            $auditData['OrderItemCalculationAudit']['billing_type'] = $calculationData['billing_type'];
            $auditData['OrderItemCalculationAudit']['tier_quantity'] = $calculationData['tier_quantity'];
            $auditData['OrderItemCalculationAudit']['proportion_used'] = $calculationData['proportion_used'];
            $auditData['OrderItemCalculationAudit']['total_supplier_amount'] = $calculationData['total_supplier_amount'];
            
            if (isset($calculationData['tier_used'])) {
                $tier = $calculationData['tier_used'];
                $auditData['OrderItemCalculationAudit']['tier_id'] = $tier['id'];
                $auditData['OrderItemCalculationAudit']['tier_range_from'] = $tier['de_qtd'];
                $auditData['OrderItemCalculationAudit']['tier_range_to'] = $tier['ate_qtd'];
                $auditData['OrderItemCalculationAudit']['tier_percentage'] = $tier['percentual_repasse_nao_formatado'];
            }
        }

        $model->create();
        return $model->save($auditData);
    }

    /**
     * Get calculation history for an order item
     */
    public static function getCalculationHistory($orderItemId)
    {
        $model = ClassRegistry::init('OrderItemCalculationAudit');
        
        return $model->find('all', [
            'conditions' => ['OrderItemCalculationAudit.order_item_id' => $orderItemId],
            'contain' => ['Supplier', 'SupplierVolumeTier'],
            'order' => ['OrderItemCalculationAudit.calculated_at' => 'DESC']
        ]);
    }

    /**
     * Get calculation summary for an order
     */
    public static function getOrderCalculationSummary($orderId)
    {
        $model = ClassRegistry::init('OrderItemCalculationAudit');
        
        return $model->find('all', [
            'joins' => [
                [
                    'table' => 'order_items',
                    'alias' => 'OrderItem',
                    'type' => 'INNER',
                    'conditions' => ['OrderItemCalculationAudit.order_item_id = OrderItem.id']
                ]
            ],
            'conditions' => ['OrderItem.order_id' => $orderId],
            'contain' => ['Supplier', 'SupplierVolumeTier'],
            'order' => ['OrderItemCalculationAudit.calculated_at' => 'DESC']
        ]);
    }

    /**
     * Generate human-readable calculation explanation
     */
    public function getCalculationExplanation()
    {
        $data = $this->data['OrderItemCalculationAudit'];
        
        switch ($data['calculation_type']) {
            case 'fixed_value':
                return "Valor fixo: R$ " . number_format($data['calculated_fee'], 2, ',', '.');
                
            case 'fixed_percentage':
                $percentage = ($data['calculated_fee'] / $data['base_value']) * 100;
                return sprintf("Percentual fixo: %.2f%% sobre R$ %s = R$ %s", 
                    $percentage,
                    number_format($data['base_value'], 2, ',', '.'),
                    number_format($data['calculated_fee'], 2, ',', '.')
                );
                
            case 'volume_tier':
                $explanation = sprintf("Tabela de volume (%s): ", 
                    $data['billing_type'] == 'cpf' ? 'Por CPF' : 'Por Pedido'
                );
                
                if ($data['tier_range_from'] && $data['tier_range_to']) {
                    $explanation .= sprintf("Quantidade %s na faixa %d-%d (%s%%), ",
                        number_format($data['tier_quantity'], 2, ',', '.'),
                        $data['tier_range_from'],
                        $data['tier_range_to'],
                        number_format($data['tier_percentage'], 2, ',', '.')
                    );
                }
                
                if ($data['proportion_used']) {
                    $explanation .= sprintf("proporção %.4f do total R$ %s = R$ %s",
                        $data['proportion_used'],
                        number_format($data['total_supplier_amount'], 2, ',', '.'),
                        number_format($data['calculated_fee'], 2, ',', '.')
                    );
                }
                
                return $explanation;
                
            default:
                return "Cálculo desconhecido";
        }
    }
}