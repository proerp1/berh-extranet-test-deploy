<?php
class OrderItem extends AppModel {
    public $name = 'OrderItem';
    public $useTable = 'order_items';
    public $primaryKey = 'id';

    public $actsAs = [
        'Containable',
    ];

    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ),
        'CustomerUserItinerary' => array(
            'className' => 'CustomerUserItinerary',
            'foreignKey' => 'customer_user_itinerary_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ),
        'PixStatus' => array(
            'className' => 'Status',
            'foreignKey' => 'pix_status_id',
            'conditions' => ['PixStatus.categoria' => 23]
        ),
    );
    
    public $virtualFields = [
        'valor_unit' => 
            'CASE WHEN OrderItem.manual_quantity > 0 ' .
            'THEN OrderItem.price_per_day / OrderItem.manual_quantity ' .
            'ELSE OrderItem.price_per_day END'
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['OrderItem.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['transfer_fee'])) {
                $results[$key][$this->alias]['transfer_fee_not_formated'] = $results[$key][$this->alias]['transfer_fee'];
                $results[$key][$this->alias]['transfer_fee'] = number_format($results[$key][$this->alias]['transfer_fee'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['commission_fee'])) {
                $results[$key][$this->alias]['commission_fee_not_formated'] = $results[$key][$this->alias]['commission_fee'];
                $results[$key][$this->alias]['commission_fee'] = number_format($results[$key][$this->alias]['commission_fee'], 2, ',', '.');
            }

            // var = desconto
            if (isset($val[$this->alias]['var'])) {
                $results[$key][$this->alias]['var_not_formated'] = $results[$key][$this->alias]['var'];
                $results[$key][$this->alias]['var'] = number_format($results[$key][$this->alias]['var'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['price_per_day'])) {
                $results[$key][$this->alias]['price_per_day_not_formated'] = $results[$key][$this->alias]['price_per_day'];
                $results[$key][$this->alias]['price_per_day'] = number_format($results[$key][$this->alias]['price_per_day'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['subtotal'])) {
                $results[$key][$this->alias]['subtotal_not_formated'] = $results[$key][$this->alias]['subtotal'];
                $results[$key][$this->alias]['subtotal'] = number_format($results[$key][$this->alias]['subtotal'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['saldo_transfer_fee'])) {
                $results[$key][$this->alias]['saldo_transfer_fee_not_formated'] = $results[$key][$this->alias]['saldo_transfer_fee'];
                $results[$key][$this->alias]['saldo_transfer_fee'] = number_format($results[$key][$this->alias]['saldo_transfer_fee'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['saldo_transfer_fee_not_formated'] = 0;
                $results[$key][$this->alias]['saldo_transfer_fee'] = '0,00';
            }

            if (isset($val[$this->alias]['saldo'])) {
                $results[$key][$this->alias]['saldo_not_formated'] = $results[$key][$this->alias]['saldo'];
                $results[$key][$this->alias]['saldo'] = number_format($results[$key][$this->alias]['saldo'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['saldo_not_formated'] = 0;
                $results[$key][$this->alias]['saldo'] = '0,00';
            }

            if (isset($val[$this->alias]['total_saldo'])) {
                $results[$key][$this->alias]['total_saldo_not_formated'] = $results[$key][$this->alias]['total_saldo'];
                $results[$key][$this->alias]['total_saldo'] = number_format($results[$key][$this->alias]['total_saldo'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['total_saldo_not_formated'] = 0;
                $results[$key][$this->alias]['total_saldo'] = '0,00';
            }

            if (isset($val[$this->alias]['data_inicio_processamento'])) {
                $data = $val[$this->alias]['data_inicio_processamento'];

                $results[$key][$this->alias]['data_inicio_processamento_nao_formatado'] = $data;
                $results[$key][$this->alias]['data_inicio_processamento'] = ($data === '0000-00-00 00:00:00' || empty($data)) ? '' : date("d/m/Y", strtotime($data));
            }

            if (isset($val[$this->alias]['data_fim_processamento'])) {
                $data = $val[$this->alias]['data_fim_processamento'];

                $results[$key][$this->alias]['data_fim_processamento_nao_formatado'] = $data;
                $results[$key][$this->alias]['data_fim_processamento'] = ($data === '0000-00-00 00:00:00' || empty($data)) ? '' : date("d/m/Y", strtotime($data));
            }

            if (isset($val[$this->alias]['data_entrega'])) {
                $results[$key][$this->alias]['data_entrega_nao_formatado'] = $val[$this->alias]['data_entrega'];
                $results[$key][$this->alias]['data_entrega'] = date("d/m/Y", strtotime($val[$this->alias]['data_entrega']));
            }

            if (isset($val[$this->alias]['valor_unit'])) {
                $results[$key][$this->alias]['valor_unit_not_formated'] = $val[$this->alias]['valor_unit'];
                $results[$key][$this->alias]['valor_unit'] = number_format($val[$this->alias]['valor_unit'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['valor_unit_not_formated'] = 0;
                $results[$key][$this->alias]['valor_unit'] = '0,00';
            }

            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
                $results[$key][$this->alias]['created'] = date("d/m/Y", strtotime($val[$this->alias]['created']));
            }

        }

        return $results;
    }

    public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['transfer_fee'])) {
			$this->data[$this->alias]['transfer_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['transfer_fee']);
		}

        if (!empty($this->data[$this->alias]['commission_fee'])) {
			$this->data[$this->alias]['commission_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['commission_fee']);
		}

        if (!empty($this->data[$this->alias]['var'])) {
			$this->data[$this->alias]['var'] = $this->priceFormatBeforeSave($this->data[$this->alias]['var']);
		}

        if (!empty($this->data[$this->alias]['price_per_day'])) {
			$this->data[$this->alias]['price_per_day'] = $this->priceFormatBeforeSave($this->data[$this->alias]['price_per_day']);
		}

        if (!empty($this->data[$this->alias]['subtotal'])) {
			$this->data[$this->alias]['subtotal'] = $this->priceFormatBeforeSave($this->data[$this->alias]['subtotal']);
		}

        if (!empty($this->data[$this->alias]['total'])) {
            $this->data[$this->alias]['total'] = $this->priceFormatBeforeSave($this->data[$this->alias]['total']);
        }

        if (!empty($this->data[$this->alias]['saldo'])) {
            $this->data[$this->alias]['saldo'] = $this->priceFormatBeforeSave($this->data[$this->alias]['saldo']);
        }

        if (!empty($this->data[$this->alias]['total_saldo'])) {
            $this->data[$this->alias]['total_saldo'] = $this->priceFormatBeforeSave($this->data[$this->alias]['total_saldo']);
        }

        if (!empty($this->data[$this->alias]['data_entrega'])) {
            $this->data[$this->alias]['data_entrega'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_entrega']);
        }

        $this->data[$this->alias]['first_order'] = $this->calculateFirstOrder();
		
		return true;
	}

    public function priceFormatBeforeSave($price)
	{
        if(is_numeric($price)){
            return $price;
        }
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

    public function dateFormatBeforeSave($dateString)
    {
        $date = DateTime::createFromFormat('d/m/Y', $dateString);

        if ($date === false) {
            $date = new DateTime($dateString);
        }

        # Check if it contains time
        if (strpos($dateString, ':') !== false) {
            return $date->format('Y-m-d H:i:s');
        }

        return $date->format('Y-m-d');
    }

    public function apiLastOrders(){
        $sql = "
        SELECT
            sum(OrderItem.subtotal) AS pedido_valor,
            CONCAT(CONCAT(CONCAT(CONCAT(CONCAT('5803-', Order.id), '-'), CustomerUser.id), '-'), Supplier.id) AS pedido_id,
            CustomerUser.id AS customer_user_id,
            CustomerUser.name AS colaborador_nome,
            CustomerUser.cpf AS colaborador_cpf,
            CustomerUser.rg AS colaborador_rg,
            CustomerUser.emissor_rg AS colaborador_emissor_rg,
            CustomerUser.emissor_estado AS colaborador_estado_emissao_rg,
            CustomerUser.sexo AS colaborador_sexo,
            CustomerUser.data_nascimento AS colaborador_data_nascimento,
            CustomerUser.nome_mae AS colaborador_nome_mae,
            CustomerUser.email AS colaborador_email,
            CustomerUserAddress.address_line AS colaborador_endereco_logradouro,
            CustomerUserAddress.address_number AS colaborador_endereco_numero,
            CustomerUserAddress.neighborhood AS colaborador_endereco_bairro,
            CustomerUserAddress.city AS colaborador_endereco_cidade,
            CustomerUserAddress.state AS colaborador_endereco_estado,
            CustomerUserAddress.zip_code AS colaborador_endereco_cep,
            CustomerUserAddress.address_complement AS colaborador_endereco_complemento,
            Customer.nome_primario AS empresa_razao_social,
            Customer.documento AS empresa_cnpj,
            Customer.nome_secundario AS empresa_nome_reduzido,
            Customer.endereco AS empresa_endereco_logradouro,
            Customer.numero AS empresa_endereco_numero,
            Customer.bairro AS empresa_endereco_bairro,
            Customer.cidade AS empresa_endereco_cidade,
            Customer.estado AS empresa_endereco_estado,
            Customer.cep AS empresa_endereco_cep,
            Customer.complemento AS empresa_endereco_complemento,
            Supplier.nome_fantasia
        FROM
            order_items OrderItem
        INNER JOIN orders `Order` on OrderItem.order_id = Order.id
        INNER JOIN customer_users CustomerUser ON OrderItem.customer_user_id = CustomerUser.id
        INNER JOIN customer_user_addresses CustomerUserAddress on CustomerUser.id = CustomerUserAddress.customer_user_id
		AND CustomerUserAddress.address_type_id = 1 
		AND CustomerUserAddress.data_cancel = '1901-01-01'
        INNER JOIN customers Customer ON CustomerUser.customer_id = Customer.id
        INNER JOIN customer_user_itineraries CustomerUserItinerary on OrderItem.customer_user_itinerary_id = CustomerUserItinerary.id
        INNER JOIN benefits Benefit on CustomerUserItinerary.benefit_id = Benefit.id
        INNER JOIN suppliers Supplier on Benefit.supplier_id = Supplier.id
        WHERE OrderItem.data_cancel = '1901-01-01 00:00:00'
        and `Order`.data_cancel = '1901-01-01 00:00:00'
        and CustomerUser.data_cancel = '1901-01-01 00:00:00'
        and Customer.data_cancel = '1901-01-01 00:00:00'
        and `Order`.status_id = 85
        GROUP BY
            Order.id, OrderItem.customer_user_id, Supplier.id
        ";

        return $this->query($sql);
    }

    public function apiBenficiaryCurrentOrders($data, $customer_user_id){
        $dateOneMonthAgo = date('Y-m-d', strtotime('-1 month', strtotime($data)));

        // SUM(i.subtotal-IFNULL(i.saldo, 0)) AS valor_credito,

        $sql = "
        SELECT b.cpf, b.name AS beneficiario, c.nome_secundario AS cliente,
            o.credit_release_date AS data_credito,
            be.name AS beneficio,
            SUM(i.subtotal) AS valor_credito,
            o.end_date as data_liberacao_credito,
            CONCAT(CONCAT(CONCAT(CONCAT(CONCAT('5803-', o.id), '-'), c.id), '-'), su.id) AS pedido_id,
            o.id as pedido_numero,
            o.is_partial,
            sum(i.saldo) AS saldo,
            i.status_processamento
        FROM customer_users b
            INNER JOIN customers c ON c.id = b.customer_id AND c.data_cancel = '1901-01-01' AND c.status_id = 3
            INNER JOIN orders o ON o.customer_id = c.id AND o.data_cancel = '1901-01-01' AND o.status_id IN (85, 86, 87)
            INNER JOIN order_items i ON i.order_id = o.id AND i.data_cancel = '1901-01-01' AND i.customer_user_id = b.id
            INNER JOIN customer_user_itineraries ci ON ci.id = i.customer_user_itinerary_id
            INNER JOIN benefits be ON be.id = ci.benefit_id 
            INNER JOIN suppliers su ON su.id = be.supplier_id
        WHERE b.data_cancel = '1901-01-01'
	AND o.status_id = 87
        AND 
	(
		(
			o.order_period_from > '".$data."'
   		)
  		OR
    		(
	            (o.order_period_from BETWEEN '".$dateOneMonthAgo."' AND '".$data."')
	            OR
	            (o.order_period_to BETWEEN '".$dateOneMonthAgo."' AND '".$data."')
	        )
	 )
        AND i.customer_user_id = ".$customer_user_id."
        GROUP BY b.id, su.id, o.id, i.status_processamento
        ORDER BY beneficiario, beneficio
        ";

        return $this->query($sql);
    }

    public function getProcessamentoPedido($type, $conditions)
    {
        return $this->find($type, [
            'fields' => [
                'Order.*',
                'OrderItem.*',
                'Customer.*',
                'Status.*',
                'CustomerUser.*',
                'Supplier.*',
                'Benefit.*',
                'CustomerUserItinerary.*',
                'CostCenter.*',
                'EconomicGroups.name',
                'EconomicGroups.document',
                'EconomicGroups.razao_social',
                'CustomerDepartments.*',
                'group_concat(OrderBalance.observacao SEPARATOR ", ") as obs',
                'BenefitType.name',
                "(CASE WHEN Order.condicao_pagamento = 1 THEN 'Pré pago' WHEN Order.condicao_pagamento = 2 THEN 'Faturado' ELSE '' END) AS Order__desc_condicao_pagamento",
				'StatusOutcome.*',
            ],
            'conditions' => $conditions,
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Order.customer_id = Customer.id'
                    ]
                ],
                [
                    'table' => 'statuses',
                    'alias' => 'Status',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Order.status_id = Status.id'
                    ]
                ],
                [
                    'table' => 'cost_center',
                    'alias' => 'CostCenter',
                    'type' => 'LEFT',
                    'conditions' => [
                        'CustomerUser.customer_cost_center_id = CostCenter.id'
                    ]
                ],
                [
                    'table' => 'economic_groups',
                    'alias' => 'EconomicGroups',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Order.economic_group_id = EconomicGroups.id'
                    ]
                ],
                [
                    'table' => 'customer_departments',
                    'alias' => 'CustomerDepartments',
                    'type' => 'LEFT',
                    'conditions' => [
                        'CustomerUser.customer_departments_id = CustomerDepartments.id'
                    ]
                ],
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ],
                [
                    'table' => 'benefit_types',
                    'alias' => 'BenefitType',
                    'type' => 'LEFT',
                    'conditions' => [
                        'BenefitType.id = Benefit.benefit_type_id'
                    ]
                ],
                [
                    'table' => 'order_balances',
                    'alias' => 'OrderBalance',
                    'type' => 'LEFT',
                    'conditions' => [
                        'OrderBalance.order_item_id = OrderItem.id', 'OrderBalance.tipo' => 3, 'OrderBalance.data_cancel' => '1901-01-01'
                    ]
                ],
                [
                    'table' => 'outcomes',
                    'alias' => 'Outcome',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Outcome.id = OrderItem.outcome_id'
                    ]
                ],
                [
                    'table' => 'statuses',
                    'alias' => 'StatusOutcome',
                    'type' => 'LEFT',
                    'conditions' => [
                        'StatusOutcome.id = Outcome.status_id'
                    ]
                ],
            ],
            'group' => ['OrderItem.id'],
            'order' => ['trim(CustomerUser.name)']
        ]);
    }

    public function calculateFirstOrder()
    {
        if (empty($this->data[$this->alias]['customer_user_id']) || 
            empty($this->data[$this->alias]['customer_user_itinerary_id'])) {
            return 0;
        }

        $customerUserId = $this->data[$this->alias]['customer_user_id'];
        $itineraryId = $this->data[$this->alias]['customer_user_itinerary_id'];
        $currentOrderItemId = isset($this->data[$this->alias]['id']) ? $this->data[$this->alias]['id'] : null;

        $CustomerUser = ClassRegistry::init('CustomerUser');
        $CustomerUserItinerary = ClassRegistry::init('CustomerUserItinerary');

        $customerUser = $CustomerUser->find('first', [
            'conditions' => ['CustomerUser.id' => $customerUserId],
            'fields' => ['CustomerUser.cpf'],
            'recursive' => -1
        ]);

        if (empty($customerUser)) {
            return 0;
        }

        $cpf = str_pad($customerUser['CustomerUser']['cpf'], 11, '0', STR_PAD_LEFT);

        $itinerary = $CustomerUserItinerary->find('first', [
            'conditions' => ['CustomerUserItinerary.id' => $itineraryId],
            'fields' => ['Benefit.supplier_id'],
            'contain' => ['Benefit']
        ]);

        if (empty($itinerary)) {
            return 0;
        }

        $supplierId = $itinerary['Benefit']['supplier_id'];

        $conditions = [
            'CustomerUser.cpf' => $cpf,
            'Benefit.supplier_id' => $supplierId,
            'Order.status_id NOT IN(83,94)',
            'Order.is_partial <>' => 3,
            'OrderItem.data_cancel' => '1901-01-01 00:00:00',
            'Order.data_cancel' => '1901-01-01 00:00:00',
        ];

        if ($currentOrderItemId) {
            $conditions['OrderItem.id <>'] = $currentOrderItemId;
        }

        $existingOrder = $this->find('first', [
            'joins' => [
                [
                    'table' => 'customer_users',
                    'alias' => 'CustomerUser',
                    'type' => 'INNER',
                    'conditions' => ['CustomerUser.id = OrderItem.customer_user_id']
                ],
                [
                    'table' => 'customer_user_itineraries',
                    'alias' => 'Itinerary',
                    'type' => 'INNER',
                    'conditions' => ['Itinerary.id = OrderItem.customer_user_itinerary_id']
                ],
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => ['Benefit.id = Itinerary.benefit_id']
                ]
            ],
            'contain' => ['Order'],
            'conditions' => $conditions,
            'fields' => ['OrderItem.id'],
            'limit' => 1
        ]);

        return empty($existingOrder) ? 1 : 0;
    }
}
