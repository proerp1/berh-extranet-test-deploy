<?php
App::uses('CakeEmail', 'Network/Email');
class Order extends AppModel
{
    public $name = 'Order';
    public $useTable = 'orders';
    public $primaryKey = 'id';

    public $actsAs = ['Containable'];
    
    public $virtualFields = [
        'desc_condicao_pagamento' => 
        "CASE 
            WHEN Order.condicao_pagamento = 1 THEN 'Pré pago' 
            WHEN Order.condicao_pagamento = 2 THEN 'Faturado' 
            ELSE '' 
        END"
    ];

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
        ],
        'EconomicGroup',
        'UpdatedGe' => [
            'className' => 'User',
            'foreignKey' => 'user_updated_ge_id'
        ],
        'CustomerAddress'
    ];

    public $hasOne = [
        'Income' => [
            'className' => 'Income',
            'foreignKey' => 'order_id',
            'conditions' => ['Income.data_cancel' => '1901-01-01 00:00:00']
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
            $results[$key][$this->alias]['transfer_fee_not_formated'] = 0;
            if (isset($val[$this->alias]['transfer_fee'])) {
                $results[$key][$this->alias]['transfer_fee_not_formated'] = $results[$key][$this->alias]['transfer_fee'];
                $results[$key][$this->alias]['transfer_fee'] = number_format($results[$key][$this->alias]['transfer_fee'], 2, ',', '.');
            }

            $results[$key][$this->alias]['commission_fee_not_formated'] = 0;
            if (isset($val[$this->alias]['commission_fee'])) {
                $results[$key][$this->alias]['commission_fee_not_formated'] = $results[$key][$this->alias]['commission_fee'];
                $results[$key][$this->alias]['commission_fee'] = number_format($results[$key][$this->alias]['commission_fee'], 2, ',', '.');
            }

            $results[$key][$this->alias]['subtotal_not_formated'] = 0;
            if (isset($val[$this->alias]['subtotal'])) {
                $results[$key][$this->alias]['subtotal_not_formated'] = $results[$key][$this->alias]['subtotal'];
                $results[$key][$this->alias]['subtotal'] = number_format($results[$key][$this->alias]['subtotal'], 2, ',', '.');
            }

            $results[$key][$this->alias]['total_not_formated'] = 0;
            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            }

            $results[$key][$this->alias]['saldo_transfer_fee_not_formated'] = 0;
            if (isset($val[$this->alias]['saldo_transfer_fee'])) {
                $results[$key][$this->alias]['saldo_transfer_fee_not_formated'] = $results[$key][$this->alias]['saldo_transfer_fee'];
                $results[$key][$this->alias]['saldo_transfer_fee'] = number_format($results[$key][$this->alias]['saldo_transfer_fee'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['saldo_transfer_fee_not_formated'] = 0;
                $results[$key][$this->alias]['saldo_transfer_fee'] = '0,00';
            }

            $results[$key][$this->alias]['saldo_not_formated'] = 0;
            if (isset($val[$this->alias]['saldo'])) {
                $results[$key][$this->alias]['saldo_not_formated'] = $results[$key][$this->alias]['saldo'];
                $results[$key][$this->alias]['saldo'] = number_format($results[$key][$this->alias]['saldo'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['saldo_not_formated'] = 0;
                $results[$key][$this->alias]['saldo'] = '0,00';
            }

            $results[$key][$this->alias]['fee_saldo_not_formated'] = 0;
            if (isset($val[$this->alias]['fee_saldo'])) {
                $results[$key][$this->alias]['fee_saldo_not_formated'] = $results[$key][$this->alias]['fee_saldo'];
                $results[$key][$this->alias]['fee_saldo'] = number_format($results[$key][$this->alias]['fee_saldo'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['fee_saldo_not_formated'] = 0;
                $results[$key][$this->alias]['fee_saldo'] = '0,00';
            }

            $results[$key][$this->alias]['total_saldo_not_formated'] = 0;
            if (isset($val[$this->alias]['total_saldo'])) {
                $results[$key][$this->alias]['total_saldo_not_formated'] = $results[$key][$this->alias]['total_saldo'];
                $results[$key][$this->alias]['total_saldo'] = number_format($results[$key][$this->alias]['total_saldo'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['total_saldo_not_formated'] = 0;
                $results[$key][$this->alias]['total_saldo'] = '0,00';
            }

            $results[$key][$this->alias]['desconto_not_formated'] = 0;
            if (isset($val[$this->alias]['desconto'])) {
                $results[$key][$this->alias]['desconto_not_formated'] = $results[$key][$this->alias]['desconto'];
                $results[$key][$this->alias]['desconto'] = number_format($results[$key][$this->alias]['desconto'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['desconto_not_formated'] = 0;
                $results[$key][$this->alias]['desconto'] = '0,00';
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

            $results[$key][$this->alias]['due_date_nao_formatado'] = null;
            if (isset($val[$this->alias]['due_date'])) {
                $results[$key][$this->alias]['due_date_nao_formatado'] = $val[$this->alias]['due_date'];
                $results[$key][$this->alias]['due_date'] = date("d/m/Y", strtotime($val[$this->alias]['due_date']));
            }

            if (isset($val[$this->alias]['updated_ge'])) {
                $results[$key][$this->alias]['updated_ge_nao_formatado'] = $val[$this->alias]['updated_ge'];
                $results[$key][$this->alias]['updated_ge'] = date("d/m/Y", strtotime($val[$this->alias]['updated_ge']));
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

        if (!empty($this->data[$this->alias]['saldo'])) {
            $this->data[$this->alias]['saldo'] = $this->priceFormatBeforeSave($this->data[$this->alias]['saldo']);
        }

        if (!empty($this->data[$this->alias]['total_saldo'])) {
            $this->data[$this->alias]['total_saldo'] = $this->priceFormatBeforeSave($this->data[$this->alias]['total_saldo']);
        }

        if (!empty($this->data[$this->alias]['desconto'])) {
            $this->data[$this->alias]['desconto'] = $this->priceFormatBeforeSave($this->data[$this->alias]['desconto']);
        }

        if (!empty($this->data[$this->alias]['fee_saldo'])) {
            $this->data[$this->alias]['fee_saldo'] = $this->priceFormatBeforeSave($this->data[$this->alias]['fee_saldo']);
        }

        if (!empty($this->data[$this->alias]['saldo_transfer_fee'])) {
            $this->data[$this->alias]['saldo_transfer_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['saldo_transfer_fee']);
        }

        if (!empty($this->data[$this->alias]['porcentagem_margem_seguranca'])) {
            $this->data[$this->alias]['porcentagem_margem_seguranca'] = $this->priceFormatBeforeSave($this->data[$this->alias]['porcentagem_margem_seguranca']);
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

        if (!empty($this->data[$this->alias]['due_date'])) {
            $this->data[$this->alias]['due_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['due_date']);
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

        if (!empty($this->data[$this->alias]['updated_ge'])) {
            $this->data[$this->alias]['updated_ge'] = $this->dateFormatBeforeSave($this->data[$this->alias]['updated_ge']);
        }
        
        $this->_ajustaStatusPorGE();

        $this->transactionNotifications($this->data[$this->alias]);

        return true;
    }

    private function _ajustaStatusPorGE() 
    { 
        if (!empty($this->data[$this->alias]['id'])) {
            $registroAtual = $this->find('first', [
                'conditions' => ['id' => $this->data[$this->alias]['id']],
                'fields' => ['status_id', 'pedido_complementar'],
                'recursive' => -1
            ]);

            $statusAtual = $registroAtual[$this->alias]['status_id'];
            $geAntigo = $registroAtual[$this->alias]['pedido_complementar'];

            $geNovo = isset($this->data[$this->alias]['pedido_complementar']) 
                        ? $this->data[$this->alias]['pedido_complementar'] 
                        : $geAntigo;

            $statusNovo = isset($this->data[$this->alias]['status_id']) 
                            ? $this->data[$this->alias]['status_id'] 
                            : $statusAtual;

            // ====================
            // Regra 1: Pagamento Confirmado (85) -> GE de 1 para 2 -> muda para 104
            // ====================
            //if ($statusNovo == 85 && $geAntigo == 1 && $geNovo == 2) {
            if ($statusNovo == 85 && $geNovo == 2) {
                $this->data[$this->alias]['status_id'] = 104;
            }

            // ====================
            // Regra 2: Aguardando Liberação de Crédito (104) -> GE de 2 para 1 -> muda para 85
            // ====================
            if ($statusAtual == 104 && $geAntigo == 2 && $geNovo == 1) {
                $this->data[$this->alias]['status_id'] = 85;
            }
        }

        return true;
    }

    public function transactionNotifications($data)
    {
        if (isset($data['id']) && isset($data['status_id'])) {
            $old = $this->find('first', [
                'conditions' => [
                    'Order.id' => $data['id']
                ],
                'recursive' => -1
            ]);

            if ($old['Order']['status_id'] != $data['status_id']) {
                $status = $this->Status->find('first', [
                    'conditions' => [
                        'Status.id' => $data['status_id']
                    ],
                    'recursive' => -1
                ]);
                $customer = $this->Customer->find('first', [
                    'conditions' => [
                        'Customer.id' => $old['Order']['customer_id']
                    ],
                    'recursive' => -1
                ]);

                $email  = trim($customer['Customer']['email']);
                $email1 = trim($customer['Customer']['email1']);

                $emails[$email] = $customer['Customer']['nome_secundario'];

                if ($email1 != '') {
                    $emails[$email1] = $customer['Customer']['nome_primario'];
                }

                $bccs = [];
                if ($old['Order']['status_id'] == 83 && $data['status_id'] == 84) {
                    $bccs['ti@berh.com.br'] = 'BERH';
                }

                $mensagem = null;
                if ($data['status_id'] == 83) {
                    $mensagem = 'Seu pedido foi gerado com sucesso em: ' . date('d/m/Y \à\s H:i\h\s', strtotime($old['Order']['created_nao_formatado']));
                } else if ($data['status_id'] == 84) {
                    $mensagem = 'Seu boleto foi gerado e aguarda pagamento para avançar na liberação junto as operadoras. <br> ' . date('d/m/Y \à\s H:i\h\s');
                } else if ($data['status_id'] == 85) {
                    $mensagem = 'Em ' . date('d/m/Y \à\s H:i\h\s') . ' seu pedido foi confirmado pagamento. A partir de agora iniciaremos o processamento do seu pedido junto as Operadoras.';
                } else if ($data['status_id'] == 86) {
                    $mensagem = 'Aguarde próxima atualização de Status.';
                } else if ($data['status_id'] == 104) {
                    $mensagem = 'Aguarde próxima atualização de Status.';
                } else if ($data['status_id'] == 87) {
                    $mensagem = 'Em ' . date('d/m/Y \à\s H:i\h\s') . ' seu pedido teve o processo concluído nas Operadoras e os Créditos foram disponibilizados conforme programação.';
                }

                $dados = [
                    'viewVars' => [
                        'tos' => $emails,
                        'bccs' => $bccs,
                        'mensagem' => $mensagem
                    ],
                    'template' => 'email_transacional',
                    'layout' => 'default',
                    'subject' => 'Atualização de pedido ' . $old['Order']['id'],
                    'config' => 'default',
                ];

                $this->sendMail($dados);
            }
        }
    }

    public function sendMail($dados)
    {
        $key = Configure::read('sendgridKey');
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@berh.com.br", "BeRH");
        $email->setReplyTo("operacao@berh.com.br", "BeRH");
        $email->setSubject($dados['subject']);

        $email->addTos($dados['viewVars']['tos']);
        if (!empty($dados['viewVars']['bccs'])) {
            $email->addBccs($dados['viewVars']['bccs']);
        }

        $html = $this->generateHTML($dados);

        $email->addContent("text/html", $html);
        $sendgrid = new \SendGrid($key);
        try {
            $response = $sendgrid->send($email);

            if ($response->statusCode() != '202') {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function generateHTML($dados)
    {
        $ce = new CakeEmail();
        $ce->viewVars($dados['viewVars']);
        if (isset($dados['layout'])) {
            $ce->template($dados['template'], $dados['layout']);
        } else {
            $ce->template($dados['template']);
        }

        $ce->emailFormat('html');

        // Funcao customizada, se atualizara o cakephp, verificar se ainda funciona
        $ce->customRender();

        return $ce->message('html');
    }

    public function reProcessAmounts()
    {
        // First, recalculate volume tier fees for all items
        $this->recalculateVolumeTransferFees();
        
        $order = $this->find('first', [
            'conditions' => [
                'Order.id' => $this->id
            ],
            'fields' => [
                'Order.customer_id',
                'Order.economic_group_id',
                'Order.desconto'
            ]
        ]);

        $proposal = $this->getProposalForOrder($order['Order']['customer_id'], $order['Order']['economic_group_id']);
        
        $tpp_fee = 0;
        if (!empty($proposal)) {
            $tpp_fee = $proposal['tpp_not_formatted'];
        }

        $items = $this->OrderItem->find('first', [
            'conditions' => [
                'Order.id' => $this->id
            ],
            'fields' => [
                'SUM(OrderItem.commission_fee) as commission_fee',
                'SUM(OrderItem.transfer_fee) as transfer_fee',
                'SUM(OrderItem.subtotal) as subtotal',
                'SUM(OrderItem.total) as total'
            ],
        ]);

        $items[0]['total'] = $items[0]['subtotal']
            + $tpp_fee
            + $items[0]['commission_fee']
            + $items[0]['transfer_fee'];

        if (!empty($order['Order']['desconto_not_formated']) && $order['Order']['desconto_not_formated'] > 0) {
            $items[0]['total'] = $items[0]['total'] - $order['Order']['desconto_not_formated'];
        }

        $commissionFee = $items[0]['commission_fee'];
        $transferFee = $items[0]['transfer_fee'];
        $subtotal = $items[0]['subtotal'];
        $total = $items[0]['total'];

        $this->save(['Order' => [
            'id' => $this->id,
            'transfer_fee' => $transferFee,
            'commission_fee' => $commissionFee,
            'tpp_fee' => $tpp_fee,
            'subtotal' => $subtotal,
            'total' => $total
        ]]);
    }

    public function reprocessFirstOrder($orderId)
    {
        $OrderItem = ClassRegistry::init('OrderItem');

        $orderItems = $OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $orderId],
            'fields' => ['OrderItem.id', 'OrderItem.customer_user_id', 'OrderItem.customer_user_itinerary_id'],
            'recursive' => -1
        ]);

        foreach ($orderItems as $orderItem) {
            // Set the data for calculateFirstOrder to work with
            $OrderItem->data = ['OrderItem' => $orderItem['OrderItem']];
            
            $firstOrderValue = $OrderItem->calculateFirstOrder();
            
            $OrderItem->updateAll(
                ['OrderItem.first_order' => $firstOrderValue],
                ['OrderItem.id' => $orderItem['OrderItem']['id']]
            );
        }

        return true;
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

    public function getExtrato($id)
    {
        $sql_bal = "SELECT  
                            o.fee_saldo,
                            o.transfer_fee,
                            o.subtotal,
                            o.total,
                            o.desconto,
                            SUM(CASE WHEN b.tipo = 1 THEN b.total END) AS total_bal_economia, 
                            SUM(CASE WHEN b.tipo = 2 AND b.total > 0 THEN b.total END) AS total_bal_ajuste_cred, 
                            SUM(CASE WHEN b.tipo = 2 AND b.total < 0 THEN b.total END) AS total_bal_ajuste_deb, 
                            SUM(CASE WHEN b.tipo = 3 THEN b.total END) AS total_bal_inconsistencia 
                        FROM orders o 
                            INNER JOIN order_balances b ON o.id = b.order_id 
                                                            AND b.data_cancel = '1901-01-01 00:00:00' 
                                                            AND b.tipo IN(1, 2, 3) 
                        WHERE o.id = :order_id 
                                AND o.data_cancel = '1901-01-01 00:00:00' 
                    ";
        $ex_bal = $this->query($sql_bal, ['order_id' => $id]);

        $v_total_bal_economia           = $ex_bal[0][0]['total_bal_economia'];
        $v_total_bal_ajuste_cred        = $ex_bal[0][0]['total_bal_ajuste_cred'];
        $v_total_bal_ajuste_deb         = $ex_bal[0][0]['total_bal_ajuste_deb'];
        $v_total_bal_inconsistencia     = $ex_bal[0][0]['total_bal_inconsistencia'];
        $fee_saldo                      = $ex_bal[0]['o']["fee_saldo"];
        $transfer_fee                   = $ex_bal[0]['o']["transfer_fee"];
        $subtotal                       = $ex_bal[0]['o']["subtotal"];
        $total                          = $ex_bal[0]['o']["total"];
        $desconto                       = $ex_bal[0]['o']["desconto"];

        $v_vl_economia          = $v_total_bal_economia;
        $v_fee_economia         = 0;

        if ($fee_saldo != 0 and $v_vl_economia != 0) {
            $v_fee_economia     = (($fee_saldo / 100) * $v_vl_economia);
        }

        $v_vl_economia              = ($v_vl_economia - $v_fee_economia);
        $v_total_economia           = ($v_vl_economia + $v_fee_economia);
        $v_perc_repasse             = (($subtotal != 0) ? ($transfer_fee / $subtotal) : 0);
        $v_repasse_economia         = ($v_perc_repasse * $v_total_economia);
        $v_valor_pedido_compra      = ($total - $v_total_economia);
        $v_repasse_pedido_compra    = ($v_perc_repasse * $v_valor_pedido_compra);
        $v_diferenca_repasse        = ($transfer_fee - $v_repasse_pedido_compra);
        $v_saldo                    = ($v_total_bal_economia - $desconto);

        $v_total_vlca   = ($v_vl_economia + $v_total_bal_ajuste_cred + $v_total_bal_ajuste_deb + $v_total_bal_inconsistencia);

        $data = [
            'v_fee_economia'                => $v_fee_economia,
            'v_total_bal_economia'          => $v_total_bal_economia,
            'v_total_bal_ajuste_cred'       => $v_total_bal_ajuste_cred,
            'v_total_bal_ajuste_deb'        => $v_total_bal_ajuste_deb,
            'v_total_bal_inconsistencia'    => $v_total_bal_inconsistencia,
            'v_total_vlca'                  => $v_total_vlca,
            'v_vl_economia'                 => $v_vl_economia,
            'v_total_economia'              => $v_total_economia,
            'v_perc_repasse'                => $v_perc_repasse,
            'v_repasse_economia'            => $v_repasse_economia,
            'v_valor_pedido_compra'         => $v_valor_pedido_compra,
            'v_repasse_pedido_compra'       => $v_repasse_pedido_compra,
            'v_diferenca_repasse'           => $v_diferenca_repasse,
            'v_saldo'                       => $v_saldo,
        ];

        return $data;
    }

    public function atualizarStatusPagamento($id)
    {
        if (empty($id)) {
            return false;
        }

        $order = $this->find('first', [
            'conditions' => ['Order.id' => $id],
            'fields' => ['Order.pedido_complementar', 'Order.condicao_pagamento'],
            'recursive' => -1
        ]);

        if (empty($order)) {
            return false;
        }

        if ($order['Order']['condicao_pagamento'] == 2) {
            $statusId = 87;
        } else {
            $statusId = ($order['Order']['pedido_complementar'] == 2) ? 104 : 85;
        }
        
        $this->id = $id;
        
        return $this->save([
            'Order' => [
                'status_id' => $statusId,
                'payment_date' => $this->getNextWeekdayDate('Y-m-d'),
            ]
        ]);
    }

    private function getNextWeekdayDate($format = 'Y-m-d H:i:s') {
        $date = new DateTime();
        $dayOfWeek = $date->format('w'); // 0 = domingo, 6 = sábado

        if ($dayOfWeek == 6) {
            $date->modify('+2 days'); // Sábado → Segunda
        } elseif ($dayOfWeek == 0) {
            $date->modify('+1 day'); // Domingo → Segunda
        }

        return $date->format($format);
    }

    /**
     * Recalculates transfer fees for all order items using RepaymentCalculator
     */
    public function recalculateVolumeTransferFees()
    {
        // Get all order items with supplier information
        $orderItems = $this->OrderItem->find('all', [
            'contain' => [
                'CustomerUserItinerary' => [
                    'Benefit' => [
                        'Supplier'
                    ]
                ]
            ],
            'conditions' => [
                'OrderItem.order_id' => $this->id,
                'OrderItem.data_cancel' => '1901-01-01 00:00:00'
            ]
        ]);

        if (empty($orderItems)) {
            return;
        }

        // Group items by supplier
        $supplierGroups = [];
        foreach ($orderItems as $item) {
            $supplierId = $item['CustomerUserItinerary']['Benefit']['Supplier']['id'];
            $supplierGroups[$supplierId][] = $item;
        }

        // Process each supplier group using RepaymentCalculator
        foreach ($supplierGroups as $supplierId => $items) {
            $this->calculateSupplierFeesUsingRepaymentCalculator($supplierId, $items);
        }
    }

    /**
     * Calculate transfer fees for supplier using RepaymentCalculator
     */
    private function calculateSupplierFeesUsingRepaymentCalculator($supplierId, $items)
    {
        if (empty($items)) {
            return;
        }

        $supplier = $items[0]['CustomerUserItinerary']['Benefit']['Supplier'];
        $tipoCobranca = isset($supplier['tipo_cobranca']) ? $supplier['tipo_cobranca'] : 'pedido';

        // Calculate total subtotal for this supplier
        $totalSupplierSubtotal = 0;
        foreach ($items as $item) {
            $subtotalValue = isset($item['OrderItem']['subtotal_not_formated']) 
                ? $item['OrderItem']['subtotal_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
            $totalSupplierSubtotal += $subtotalValue;
        }

        if ($totalSupplierSubtotal == 0) {
            return;
        }

        // Determine quantity for calculation based on billing type
        if ($tipoCobranca == 'cpf') {
            $quantity = $this->countCustomerUsersForSupplier($supplierId);
        } else {
            $quantity = $this->getTotalAmountForSupplier($supplierId);
        }

        // Use RepaymentCalculator for all calculations
        try {
            App::uses('RepaymentCalculator', 'Lib');
            $calculationResult = RepaymentCalculator::calculateRepayment(
                $supplierId, 
                $quantity, 
                $totalSupplierSubtotal
            );
            
            $totalTransferFee = $this->parseFormattedNumber($calculationResult['repayment_value']);
            $calculationMethod = $calculationResult['calculation_method'];
            $tierUsed = $calculationResult['tier_used'];

            // Distribute fees among items based on calculation method
            if ($calculationMethod === 'volume_tier_percentage' && $calculationResult['billing_type'] == 'item') {
                // Individual percentage application
                $repaymentPercentage = $this->parseFormattedNumber($calculationResult['repayment_percentage']);
                $this->applyIndividualPercentageFees($items, $repaymentPercentage, $calculationMethod, $tierUsed);
            } else {
                // Proportional distribution (for volume tiers, fixed values, etc.)
                $this->distributeFeesProportionally($items, $totalTransferFee, $totalSupplierSubtotal, $calculationMethod, $tierUsed, $tipoCobranca);
            }

        } catch (Exception $e) {
            // Silent error handling - let the system continue
        }
    }

    /**
     * Apply individual percentage fees to each item
     */
    private function applyIndividualPercentageFees($items, $percentage, $calculationMethod, $tierUsed)
    {
        foreach ($items as $item) {
            $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
                ? $item['OrderItem']['subtotal_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
                
            $itemCommissionFee = isset($item['OrderItem']['commission_fee_not_formated']) 
                ? $item['OrderItem']['commission_fee_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['commission_fee']);

            $itemTransferFee = ($itemSubtotal * $percentage) / 100;

            $calculationLog = json_encode([
                'type' => $calculationMethod,
                'percentage' => $percentage,
                'tier_used' => $tierUsed,
                'item_subtotal' => $itemSubtotal,
                'calculated_fee' => $itemTransferFee
            ]);

            $this->updateOrderItemFees($item['OrderItem']['id'], $itemTransferFee, $itemSubtotal, $itemCommissionFee, $calculationLog);
        }
    }

    /**
     * Distribute total fees proportionally among items
     */
    private function distributeFeesProportionally($items, $totalTransferFee, $totalSupplierSubtotal, $calculationMethod, $tierUsed, $tipoCobranca)
    {
        foreach ($items as $item) {
            $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
                ? $item['OrderItem']['subtotal_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
                
            $itemCommissionFee = isset($item['OrderItem']['commission_fee_not_formated']) 
                ? $item['OrderItem']['commission_fee_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['commission_fee']);
                
            $proportion = $totalSupplierSubtotal > 0 ? ($itemSubtotal / $totalSupplierSubtotal) : 0;
            $itemTransferFee = $totalTransferFee * $proportion;

            $calculationLog = json_encode([
                'type' => $calculationMethod,
                'billing_type' => $tipoCobranca,
                'tier_used' => $tierUsed,
                'total_fee' => $totalTransferFee,
                'proportion' => $proportion,
                'calculated_fee' => $itemTransferFee
            ]);

            $this->updateOrderItemFees($item['OrderItem']['id'], $itemTransferFee, $itemSubtotal, $itemCommissionFee, $calculationLog);
        }
    }

    /**
     * Update order item fees
     */
    private function updateOrderItemFees($itemId, $transferFee, $subtotal, $commissionFee, $calculationLog)
    {
        $updateData = [
            'OrderItem' => [
                'id' => $itemId,
                'transfer_fee' => $transferFee,
                'total' => $subtotal + $transferFee + $commissionFee,
                'calculation_details_log' => $calculationLog
            ]
        ];
        
        $this->OrderItem->save($updateData, ['callbacks' => false, 'validate' => false]);
    }

    /**
     * Count unique customer users for a supplier in this order
     */
    private function countCustomerUsersForSupplier($supplierId)
    {
        $count = $this->OrderItem->find('count', [
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => ['CustomerUserItinerary.benefit_id = Benefit.id']
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => ['Benefit.supplier_id = Supplier.id']
                ]
            ],
            'conditions' => [
                'OrderItem.order_id' => $this->id,
                'Supplier.id' => $supplierId,
                'OrderItem.data_cancel' => '1901-01-01 00:00:00'
            ],
            'fields' => ['COUNT(DISTINCT OrderItem.customer_user_id) as count'],
            'group' => false
        ]);

        return $count > 0 ? $count : 1;
    }

    /**
     * Get total amount for a supplier in this order
     */
    private function getTotalAmountForSupplier($supplierId)
    {
        $result = $this->OrderItem->find('first', [
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => ['CustomerUserItinerary.benefit_id = Benefit.id']
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => ['Benefit.supplier_id = Supplier.id']
                ]
            ],
            'conditions' => [
                'OrderItem.order_id' => $this->id,
                'Supplier.id' => $supplierId,
                'OrderItem.data_cancel' => '1901-01-01 00:00:00'
            ],
            'fields' => ['SUM(OrderItem.subtotal) as total_amount'],
            'group' => false
        ]);

        $totalAmount = isset($result[0]['total_amount']) ? floatval($result[0]['total_amount']) : 0;
        return $totalAmount > 0 ? $totalAmount : 1;
    }

    /**
     * Parse a formatted number (e.g., "1.234,56") back to a float
     */
    private function parseFormattedNumber($formattedValue)
    {
        if (is_numeric($formattedValue)) {
            return floatval($formattedValue);
        }
        
        // Handle Brazilian format: 1.234,56 -> 1234.56
        $value = trim($formattedValue);
        
        // If there's both dot and comma, remove dots (thousands separator) and replace comma with dot
        if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            // If there's only comma, replace it with dot
            $value = str_replace(',', '.', $value);
        }
        
        return floatval($value);
    }

    public function getProposalForOrder($customerId, $economic_group_id = null) 
    {
        $proposal = null;

        if (!empty($economic_group_id)) {
            $economicGroupProp = ClassRegistry::init('EconomicGroupProposal');

            $economicGroupProposal = $economicGroupProp->find('first', [
                'conditions' => [
                    'EconomicGroupProposal.customer_id' => $customerId, 
                    'EconomicGroupProposal.economic_group_id' => $economic_group_id, 
                    'EconomicGroupProposal.status_id' => 99
                ]
            ]);
            
            if (!empty($economicGroupProposal)) {
                $proposal = $economicGroupProposal['EconomicGroupProposal'];
            }
        }
        
        if (empty($proposal)) {
            $prop = ClassRegistry::init('Proposal');

            $customerProposal = $prop->find('first', [
                'conditions' => ['Proposal.customer_id' => $customerId, 'Proposal.status_id' => 99]
            ]);
            
            if (!empty($customerProposal)) {
                $proposal = $customerProposal['Proposal'];
            }
        }
        
        return $proposal;
    }
}
