<?php
class Order extends AppModel
{
    public $name = 'Order';
    public $useTable = 'orders';
    public $primaryKey = 'id';

    public $actsAs = ['Containable'];

    public $belongsTo = [
        'Customer' => [
            'className' => 'Customer',
            'foreignKey' => 'customer_id'
        ],
        'Creator' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id'
        ],
        'CustomerCreator' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'user_creator_id'
        ],
        'CustomerCreator' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'user_creator_id'
        ],
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 18]
        ]
    ];
    public $hasMany = [
        'OrderItem' => [
            'className' => 'OrderItem',
            'foreignKey' => 'order_id'
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Order.data_cancel' => '1901-01-01 00:00:00'];

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

            if (isset($val[$this->alias]['subtotal'])) {
                $results[$key][$this->alias]['subtotal_not_formated'] = $results[$key][$this->alias]['subtotal'];
                $results[$key][$this->alias]['subtotal'] = number_format($results[$key][$this->alias]['subtotal'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['desconto'])) {
                $results[$key][$this->alias]['desconto_not_formated'] = $results[$key][$this->alias]['desconto'];
                $results[$key][$this->alias]['desconto'] = number_format($results[$key][$this->alias]['desconto'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['order_period_from'])) {
                $results[$key][$this->alias]['order_period_from_nao_formatado'] = $val[$this->alias]['order_period_from'];
                $results[$key][$this->alias]['order_period_from'] = date("d/m/Y", strtotime($val[$this->alias]['order_period_from']));
            }

            if (isset($val[$this->alias]['order_period_to'])) {
                $results[$key][$this->alias]['order_period_to_nao_formatado'] = $val[$this->alias]['order_period_to'];
                $results[$key][$this->alias]['order_period_to'] = date("d/m/Y", strtotime($val[$this->alias]['order_period_to']));
            }

            if (isset($val[$this->alias]['credit_release_date'])) {
                $results[$key][$this->alias]['credit_release_date_nao_formatado'] = $val[$this->alias]['credit_release_date'];
                $results[$key][$this->alias]['credit_release_date'] = date("d/m/Y", strtotime($val[$this->alias]['credit_release_date']));
            }

            if (isset($val[$this->alias]['validation_date'])) {
                $results[$key][$this->alias]['validation_date_nao_formatado'] = $val[$this->alias]['validation_date'];
                $results[$key][$this->alias]['validation_date'] = date("d/m/Y", strtotime($val[$this->alias]['validation_date']));
            }

            if (isset($val[$this->alias]['issuing_date'])) {
                $results[$key][$this->alias]['issuing_date_nao_formatado'] = $val[$this->alias]['issuing_date'];
                $results[$key][$this->alias]['issuing_date'] = date("d/m/Y", strtotime($val[$this->alias]['issuing_date']));
            }

            if (isset($val[$this->alias]['payment_date'])) {
                $results[$key][$this->alias]['payment_date_nao_formatado'] = $val[$this->alias]['payment_date'];
                $results[$key][$this->alias]['payment_date'] = date("d/m/Y", strtotime($val[$this->alias]['payment_date']));
            }

            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
                $results[$key][$this->alias]['created'] = date("d/m/Y", strtotime($val[$this->alias]['created']));
            }

            if (isset($val[$this->alias]['end_date'])) {
                $results[$key][$this->alias]['end_date_nao_formatado'] = $val[$this->alias]['end_date'];
                $results[$key][$this->alias]['end_date'] = date("d/m/Y", strtotime($val[$this->alias]['end_date']));
            }
            
        }

        return $results;
    }

    public function beforeSave($options = array())
    {
        if (!empty($this->data[$this->alias]['transfer_fee'])) {
            $this->data[$this->alias]['transfer_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['transfer_fee']);
        }

        if (!empty($this->data[$this->alias]['commission_fee'])) {
            $this->data[$this->alias]['commission_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['commission_fee']);
        }

        if (!empty($this->data[$this->alias]['subtotal'])) {
            $this->data[$this->alias]['subtotal'] = $this->priceFormatBeforeSave($this->data[$this->alias]['subtotal']);
        }

        if (!empty($this->data[$this->alias]['total'])) {
            $this->data[$this->alias]['total'] = $this->priceFormatBeforeSave($this->data[$this->alias]['total']);
        }

        if (!empty($this->data[$this->alias]['desconto'])) {
            $this->data[$this->alias]['desconto'] = $this->priceFormatBeforeSave($this->data[$this->alias]['desconto']);
        }

        if (!empty($this->data[$this->alias]['order_period_from'])) {
            $this->data[$this->alias]['order_period_from'] = $this->dateFormatBeforeSave($this->data[$this->alias]['order_period_from']);
        }

        if (!empty($this->data[$this->alias]['order_period_to'])) {
            $this->data[$this->alias]['order_period_to'] = $this->dateFormatBeforeSave($this->data[$this->alias]['order_period_to']);
        }

        if (!empty($this->data[$this->alias]['credit_release_date'])) {
            $this->data[$this->alias]['credit_release_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['credit_release_date']);
        }

        if (!empty($this->data[$this->alias]['end_date'])) {
            $this->data[$this->alias]['end_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['end_date']);
        }
        
        if (!empty($this->data[$this->alias]['validation_date'])) {
            $this->data[$this->alias]['validation_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['validation_date']);
        }

        if (!empty($this->data[$this->alias]['issuing_date'])) {
            $this->data[$this->alias]['issuing_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['issuing_date']);
        }

        if (!empty($this->data[$this->alias]['payment_date'])) {
            $this->data[$this->alias]['payment_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['payment_date']);
        }

        if (!empty($this->data[$this->alias]['created'])) {
            $this->data[$this->alias]['created'] = $this->dateFormatBeforeSave($this->data[$this->alias]['created']);
        }

        return true;
    }

    public function reProcessAmounts()
    {
        $items = $this->OrderItem->find('first', [
            'conditions' => [
                'Order.id' => $this->id
            ],
            'fields' => [
                'SUM(OrderItem.transfer_fee) as transfer_fee',
                'SUM(OrderItem.subtotal) as subtotal',
                'SUM(OrderItem.total) as total'
            ],
        ]);

        $order = $this->findById($this->id);

        $transferFee = $items[0]['transfer_fee'];
        $subtotal = $items[0]['subtotal'];
        $total = $items[0]['total'];

        $customer = $this->Customer->find('first', [
            'conditions' => [
                'Customer.id' => $order['Order']['customer_id']
            ],
            'fields' => [
                'Customer.commission_fee_percentage'
            ]
        ]);

        $commissionFeePercentage = $customer['Customer']['commission_fee_percentage'];

        $commissionFee = $subtotal * ($commissionFeePercentage / 100);

        $this->save(['Order' => [
            'id' => $this->id,
            'transfer_fee' => $transferFee,
            'commission_fee' => $commissionFee,
            'subtotal' => $subtotal,
            'total' => $total
        ]]);
    }

    public function priceFormatBeforeSave($price)
    {
        if (is_numeric($price)) {
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
}
