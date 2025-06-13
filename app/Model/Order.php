<?php
App::uses('CakeEmail', 'Network/Email');
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
        ],
        'EconomicGroup',
        'UpdatedGe' => [
            'className' => 'User',
            'foreignKey' => 'user_updated_ge_id'
        ],
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

        if (isset($this->data[$this->alias]['status_id'])) {
            $novo_status_id = $this->data[$this->alias]['status_id'];

            // Se status "Pagamento Confirmado" e GE = "não", status = "Aguardando Liberação de Crédito"
            if ($novo_status_id == 85) {
                if (!empty($this->data[$this->alias]['id'])) {
                    if (!isset($this->data[$this->alias]['pedido_complementar'])) {
                        $registroAtual = $this->find('first', [
                            'conditions' => ['id' => $this->data[$this->alias]['id']],
                            'fields' => ['pedido_complementar'],
                            'recursive' => -1
                            ]
                        );

                        $ge = isset($registroAtual[$this->alias]['pedido_complementar']) ? $registroAtual[$this->alias]['pedido_complementar'] : null;
                    } else {
                        $ge = $this->data[$this->alias]['pedido_complementar'];
                    }
                    
                    if ($ge == 2) {
                        $this->data[$this->alias]['status_id'] = 104;
                    }
                }
            }
        }

        $this->transactionNotifications($this->data[$this->alias]);

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

                $emails[$customer['Customer']['email']] = $customer['Customer']['nome_secundario'];

                if ($customer['Customer']['email1'] != '') {
                    $emails[$customer['Customer']['email1']] = $customer['Customer']['nome_primario'];
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
        $order = $this->find('first', [
            'conditions' => [
                'Order.id' => $this->id
            ],
            'fields' => [
                'Order.customer_id',
                'Order.desconto'
            ]
        ]);

        $tpp_fee = 0;
        $prop = ClassRegistry::init('Proposal');
        $proposal = $prop->find('first', [
            'conditions' => ['Proposal.customer_id' => $order['Order']['customer_id'], 'Proposal.status_id' => 99]
        ]);
        if (!empty($proposal)) {
            $tpp_fee = $proposal['Proposal']['tpp_not_formatted'];
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
        $CustomerUserItinerary = ClassRegistry::init('CustomerUserItinerary');


        $orderItems = $OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $orderId],
            'fields' => ['OrderItem.id', 'OrderItem.customer_user_id', 'OrderItem.customer_user_itinerary_id'],
            'recursive' => -1
        ]);

        foreach ($orderItems as $orderItem) {
            $customerUserId = $orderItem['OrderItem']['customer_user_id'];
            $itineraryId = $orderItem['OrderItem']['customer_user_itinerary_id'];

            $itinerary = $CustomerUserItinerary->find('first', [
                'conditions' => ['CustomerUserItinerary.id' => $itineraryId],
                'fields' => ['CustomerUserItinerary.benefit_id'],
                'recursive' => -1
            ]);

            $benefitId = isset($itinerary['CustomerUserItinerary']) ? $itinerary['CustomerUserItinerary']['benefit_id'] : null;

            // Pedido deve ser diferente do atual e status todos menos inicio e cancelado
            $firstOrder = $OrderItem->find('first', [
                'joins' => [
                    [
                        'table' => 'customer_user_itineraries',
                        'alias' => 'Itinerary',
                        'type' => 'INNER',
                        'conditions' => ['Itinerary.id = OrderItem.customer_user_itinerary_id']
                    ]
                ],
                'contain' => ['Order'],
                'conditions' => [
                    'OrderItem.customer_user_id' => $customerUserId,
                    'Itinerary.benefit_id' => $benefitId,
                    'OrderItem.order_id <>' => $orderId,
                    'Order.status_id NOT IN(83,18)',
                    'Order.is_partial <>' => 3
                ],
                'fields' => ['MIN(OrderItem.id) AS first_order_item_id'],
            ]);

            $firstOrder = $firstOrder[0]['first_order_item_id'];

            if(empty($firstOrder)) {
                $this->OrderItem->bindModel(
                    ['belongsTo' => ['Order', 'CustomerUserItinerary', 'CustomerUser']]
                );
                $OrderItem->updateAll(
                    ['OrderItem.first_order' => 1],
                    ['OrderItem.id' => $orderItem['OrderItem']['id']]
                );
            }
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
        $order = $this->find('first', ['conditions' => ['Order.id' => $id], 'recursive' => -1]);

        $sql_bal = "SELECT  COALESCE(SUM(CASE WHEN b.tipo = 1 THEN b.total ELSE 0 END), 0) AS total_bal_economia, 
                            COALESCE(SUM(CASE WHEN b.tipo = 2 AND b.total > 0 THEN b.total ELSE 0 END), 0) AS total_bal_ajuste_cred, 
                            COALESCE(SUM(CASE WHEN b.tipo = 2 AND b.total < 0 THEN b.total ELSE 0 END), 0) AS total_bal_ajuste_deb, 
                            COALESCE(SUM(CASE WHEN b.tipo = 3 THEN b.total ELSE 0 END), 0) AS total_bal_inconsistencia, 
                            GROUP_CONCAT(DISTINCT TRIM(b.observacao) SEPARATOR ' | ') AS observacoes 
                        FROM order_balances b 
                            INNER JOIN orders o ON o.id = b.order_id 
                        WHERE o.id = :order_id 
                            AND b.data_cancel = '1901-01-01 00:00:00' 
                            AND o.data_cancel = '1901-01-01 00:00:00' 
                            AND b.tipo IN(1, 2, 3) 
                    ";
        $ex_bal = $this->query($sql_bal, ['order_id' => $id]);

        $v_fee_economia     = 0;

        $v_total_bal_economia           = $ex_bal[0][0]['total_bal_economia'];
        $v_total_bal_ajuste_cred        = $ex_bal[0][0]['total_bal_ajuste_cred'];
        $v_total_bal_ajuste_deb         = $ex_bal[0][0]['total_bal_ajuste_deb'];
        $v_total_bal_inconsistencia     = $ex_bal[0][0]['total_bal_inconsistencia'];
        $v_observacao                   = $ex_bal[0][0]['observacoes'];

        $v_vl_economia      = $v_total_bal_economia;
        $fee_saldo          = $order["Order"]["fee_saldo_not_formated"];
        $transfer_fee       = $order["Order"]["transfer_fee_not_formated"];
        $subtotal           = $order["Order"]["subtotal_not_formated"];
        $total              = $order["Order"]["total_not_formated"];
        $desconto           = $order["Order"]["desconto_not_formated"];

        if ($fee_saldo != 0 and $v_vl_economia != 0) {
            $v_fee_economia   = (($fee_saldo / 100) * $v_vl_economia);
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
                    'v_observacao'                  => $v_observacao,
                ];

        return $data;
    }
}
