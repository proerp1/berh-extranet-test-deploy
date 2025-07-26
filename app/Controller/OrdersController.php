<?php
App::uses('ApiItau', 'Lib');
App::uses('ApiBtgPactual', 'Lib');

use League\Csv\Reader;

class OrdersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'HtmltoPdf', 'Uploader', 'Email'];
    public $uses = [
        'Order',
        'Customer',
        'CustomerUserItinerary',
        'Benefit',
        'OrderItem',
        'CustomerUserVacation',
        'CustomerUser',
        'Income',
        'Bank',
        'BankTicket',
        'CnabLote',
        'CnabItem',
        'PaymentImportLog',
        'EconomicGroup',
        'BenefitType',
        'Outcome',
        'Status',
        'Proposal',
        'OrderBalance',
        'Log',
        'Supplier',
        'CustomerUserAddress',
        'BancoPadrao',
        'CustomerUserBankAccount',
        'OrderBalanceFile',
        'BankAccount',
        'OrderDiscount'
    ];
    public $groupBenefitType = [
        -1 => [1, 2],
        4 => [4, 5],
        999 => [6, 7, 8, 9, 10]
    ];

    public $paginate = [
        'Order' => [
            'contain' => ['Customer', 'CustomerCreator', 'EconomicGroup', 'Status', 'Creator', 'Income'],
            'fields' => [
                'Order.*',
                'Status.id',
                'Status.label',
                'Status.name',
                'Customer.codigo_associado',
                'Customer.emitir_nota_fiscal',
                'CustomerCreator.name',
                'Creator.name',
                'EconomicGroup.name',
                'Customer.nome_primario',
                'Income.data_pagamento',
                "(SELECT coalesce(sum(b.total), 0) as total_balances 
                    FROM order_balances b 
                        INNER JOIN orders o ON o.id = b.order_id 
                    WHERE o.id = Order.id 
                            AND b.tipo = 1 
                            AND b.data_cancel = '1901-01-01 00:00:00' 
                            AND o.data_cancel = '1901-01-01 00:00:00' 
                ) as total_balances"
            ],
            'limit' => 50,
            'order' => ['Order.id' => 'desc'],
            'paramType' => 'querystring'
        ],
        'OrderBalance' => [
            'limit' => 100,
            'order' => ['CustomerUser.name' => 'asc', 'OrderBalance.document' => 'asc']

        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        ini_set('pcre.backtrack_limit', '15000000');

        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");

        $limit = !empty($this->request->query('limit')) ? (int)$this->request->query('limit') : 50;
        $this->paginate['Order']['limit'] = $limit;
        $this->Paginator->settings = $this->paginate;
        ini_set('memory_limit', '-1');

        if (!in_array(CakeSession::read("Auth.User.Group.name"), array('Administrador', 'Diretoria'))) {
            $condition = ["and" => ["Customer.id != " => 88357], "or" => []];
        } else {
            $condition = ["and" => [], "or" => []];
        }

        $filtersFilled = false;

        if (isset($_GET['q']) && $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'Order.id' => $_GET['q'],
                'Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%",
                'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%",
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%",
                'Customer.id LIKE' => "%" . $_GET['q'] . "%"
            ]);
            $filtersFilled = true;
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['t']]);
            $filtersFilled = true;
        }

        if (!empty($_GET['antecipada']) && $_GET['antecipada'] != '') {
            $comparator = $_GET['antecipada'] == 'S' ? '=' : '!=';
            $condition['and'] = array_merge($condition['and'], ["Customer.emitir_nota_fiscal $comparator 'A'"]);
            $filtersFilled = true;
        }

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        if ($get_de != '' && $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], [
                'Order.created between ? and ?' => [$de . ' 00:00:00', $ate . ' 23:59:59']
            ]);
            $filtersFilled = true;
        }

        if (!empty($_GET['tipo'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.is_partial' => $_GET['tipo']]);
            $filtersFilled = true;
        }

        $get_de_pagamento = isset($_GET['de_pagamento']) ? $_GET['de_pagamento'] : '';
        $get_ate_pagamento = isset($_GET['ate_pagamento']) ? $_GET['ate_pagamento'] : '';

        if ($get_de_pagamento != '' && $get_ate_pagamento != '') {
            $de_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_de_pagamento)));
            $ate_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate_pagamento)));

            $condition['and'] = array_merge($condition['and'], [
                'Income.data_pagamento between ? and ?' => [$de_pagamento . ' 00:00:00', $ate_pagamento . ' 23:59:59']
            ]);
            $filtersFilled = true;
        }

        if (!empty($_GET['ge'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.pedido_complementar' => $_GET['ge']]);
            $filtersFilled = true;
        }

        $queryString = http_build_query($_GET);

        if (isset($_GET['exportar'])) {
            $nome = 'pedidos' . date('d_m_Y_H_i_s') . '.xlsx';

            $data = $this->Order->find('all', [
                'contain' => [
                    'Status',
                    'Customer',
                    'CustomerCreator',
                    'EconomicGroup',
                    'Income.data_pagamento',
                    'UpdatedGe',
                ],
                'conditions' => $condition,
            ]);

            foreach ($data as $k => $pedido) {
                $suppliersCount = $this->OrderItem->find('count', [
                    'conditions' => ['OrderItem.order_id' => $pedido['Order']['id']],
                    'joins' => [
                        [
                            'table' => 'benefits',
                            'alias' => 'Benefit',
                            'type' => 'INNER',
                            'conditions' => [
                                'Benefit.id = CustomerUserItinerary.benefit_id'
                            ]
                        ],
                        [
                            'table' => 'suppliers',
                            'alias' => 'Supplier',
                            'type' => 'INNER',
                            'conditions' => [
                                'Supplier.id = Benefit.supplier_id'
                            ]
                        ]
                    ],
                    'group' => ['Supplier.id'],
                    'fields' => ['Supplier.id']
                ]);

                $usersCount = $this->OrderItem->find('count', [
                    'conditions' => ['OrderItem.order_id' => $pedido['Order']['id']],
                    'group' => ['OrderItem.customer_user_id'],
                    'fields' => ['OrderItem.customer_user_id']
                ]);

                $order_balances_total = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $pedido['Order']['id'], "OrderBalance.tipo" => 1], 'fields' => 'SUM(OrderBalance.total) as total']);

                $data[$k]['Order']['suppliersCount'] = $suppliersCount;
                $data[$k]['Order']['usersCount'] = $usersCount;
                $data[$k]["Order"]["total_balances"] = $order_balances_total[0][0]['total'];
            }

            $this->ExcelGenerator->gerarExcelPedidoscustomer($nome, $data);

            $this->redirect("/files/excel/" . $nome);
        }

        $data = $this->Paginator->paginate('Order', $condition);

        $customers = $this->Customer->find('list', [
            'conditions' => ['Customer.status_id' => 3],
            'fields' => ['id', 'nome_primario'],
            'order' => ['nome_primario' => 'asc']
        ]);

        $totalOrders = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'fields' => [
                'sum(Order.subtotal) as subtotal',
                'sum(Order.transfer_fee) as transfer_fee',
                'sum(Order.commission_fee) as commission_fee',
                'sum(Order.desconto) as desconto',
                'sum(Order.total) as total',
            ],
            'conditions' => $condition,
            'recursive' => -1
        ]);

        $benefit_types = [-1 => 'Transporte', 4 => 'PAT', 999 => 'Outros'];

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => ''];
        $this->set(compact('data', 'limit', 'status', 'action', 'breadcrumb', 'customers', 'benefit_types', 'totalOrders', 'filtersFilled', 'queryString'));
    }


    public function createOrder()
    {
        $this->autoRender = false;
        ini_set('max_execution_time', '-1');

        ini_set('memory_limit', '-1');
        if ($this->request->is('post')) {
            $customerId = $this->request->data['customer_id'];
            $customerAddressId = isset($this->request->data['customer_address_id']) ? $this->request->data['customer_address_id'] : null;
            $geraNfse = $this->request->data['gera_nfse'];
            $workingDays = $this->request->data['working_days'];
            $period_from = $this->request->data['period_from'];
            $period_to = $this->request->data['period_to'];
            $is_consolidated = $this->request->data['is_consolidated'];
            $is_partial = $this->request->data['is_partial'];
            $pedido_complementar = $this->request->data['pedido_complementar'];
            $working_days_type = $this->request->data['working_days_type'];
            $grupo_especifico = isset($this->request->data['grupo_especifico']) ? $this->request->data['grupo_especifico'] : '';
            $benefit_type = $this->request->data['benefit_type'];
            $is_beneficio = $this->request->data['is_beneficio'];
            $is_beneficio = (int)$is_beneficio;
            $benefit_type = $is_beneficio == 1 ? '' : $benefit_type;
            $credit_release_date = $this->request->data['credit_release_date'];

            if ($this->request->data['clone_order'] == 1) {
                $this->cloneOrder();
            }

            $benefit_type_persist = 0;
            if ($benefit_type != '') {
                $benefit_type_persist = $benefit_type;
                $benefit_type = (int)$benefit_type;
                if ($benefit_type == -1) {
                    $benefit_type = [1, 2];
                }
            }

            $proposal = $this->Proposal->find('first', [
                'conditions' => ['Proposal.customer_id' => $customerId, 'Proposal.status_id' => 99]
            ]);
            if (empty($proposal)) {
                $this->Flash->set(__('Cliente não possui uma proposta ativa.'), ['params' => ['class' => "alert alert-danger"]]);
                $this->redirect(['action' => 'index']);
            }

            $customer = $this->Customer->find('first', ['fields' => ['Customer.observacao_notafiscal', 'Customer.flag_gestao_economico', 'Customer.porcentagem_margem_seguranca', 'Customer.qtde_minina_diaria', 'Customer.tipo_ge'], 'conditions' => ['Customer.id' => $customerId], 'recursive' => -1]);

            if ($is_partial == 3 || $is_partial == 4) {
                $pedido_complementar = 2;
            } else {
                $pedido_complementar = ($customer['Customer']['flag_gestao_economico'] == "S" ? 1 : 2);
            }

            if ($is_consolidated == 2) {
                $b_type_consolidated = $benefit_type_persist == 0 ? '' : $benefit_type_persist;
                $orderId = $this->processConsolidated($customerId, $workingDays, $period_from, $period_to, $is_partial, $credit_release_date, $working_days_type, $grupo_especifico, $b_type_consolidated, $proposal, $pedido_complementar);
                if ($orderId) {
                    // se já foi processado, acaba a função aqui
                    $this->redirect(['action' => 'index']);
                } else {
                    // se não foi processado, continua a função
                    $this->Flash->set(__('Falha ao criar o pedido consolidado.'), ['params' => ['class' => "alert alert-danger"]]);
                    $this->redirect(['action' => 'index']);
                }
            }

            if ($is_partial == 2) {
                $condNotPartial = [
                    'CustomerUserItinerary.customer_id' => $customerId,
                    'CustomerUserItinerary.status_id' => 1,
                    'CustomerUser.id is not null',
                    'CustomerUser.status_id' => 1,
                    'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
                    'Benefit.status_id' => 1,
                    'Supplier.status_id' => 1,
                ];

                if ($benefit_type != '') {
                    $condNotPartial['Benefit.benefit_type_id'] = $benefit_type;
                }

                $customerItineraries = $this->CustomerUserItinerary->find('all', [
                    'conditions' => $condNotPartial,
                    'joins' => [
                        [
                            'table' => 'suppliers',
                            'alias' => 'Supplier',
                            'type' => 'INNER',
                            'conditions' => [
                                'Supplier.id = Benefit.supplier_id'
                            ]
                        ]
                    ]
                ]);

                if (empty($customerItineraries)) {
                    $this->Flash->set(__('Nenhum itinerário encontrado para este cliente.'), ['params' => ['class' => "alert alert-danger"]]);
                    $this->redirect(['action' => 'index']);
                }
            }

            $obs_notafiscal = "";
            if ($customer['Customer']['observacao_notafiscal']) {
                $obs_notafiscal = $customer['Customer']['observacao_notafiscal'];
            }

            $customer_orders = $this->Order->find('count', ['conditions' => ['Order.customer_id' => $customerId]]);

            $orderData = [
                'customer_id' => $customerId,
                'customer_address_id' => $customerAddressId,
                'gera_nfse' => $geraNfse,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period_from' => $period_from,
                'order_period_to' => $period_to,
                'status_id' => 83,
                'is_partial' => $is_partial,
                'pedido_complementar' => $pedido_complementar,
                'credit_release_date' => $credit_release_date,
                'created' => date('Y-m-d H:i:s'),
                'working_days_type' => $working_days_type,
                'benefit_type' => $benefit_type_persist,
                'due_date' => $this->request->data['due_date'],
                'nfse_observation' => $obs_notafiscal,
                'flag_gestao_economico' => $customer['Customer']['flag_gestao_economico'],
                'porcentagem_margem_seguranca' => $customer['Customer']['porcentagem_margem_seguranca'],
                'qtde_minina_diaria' => $customer['Customer']['qtde_minina_diaria'],
                'tipo_ge' => $customer['Customer']['tipo_ge'],
                'primeiro_pedido' => ($customer_orders > 1 ? "N" : "S"),
            ];

            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();

                if ($is_partial == 2) {
                    $this->processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type, $proposal);
                }

                $this->Order->id = $orderId;
                $this->Order->reProcessAmounts($orderId);
                $this->Order->reprocessFirstOrder($orderId);

                $this->Flash->set(__('Pedido gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('Falha ao criar pedido. Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }

            $this->redirect(['action' => 'edit/' . $orderId]);
        }
    }

    public function cloneOrder()
    {
        $lastOrder = $this->Order->find('first', [
            'contain' => ['OrderItem'],
            'conditions' => ['Order.id' => $this->request->data['clone_order_id']],
        ]);

        $is_partial = $lastOrder['Order']['is_partial'];
        $benefit_type = $lastOrder['Order']['benefit_type'];
        $working_days = $lastOrder['Order']['working_days'];
        $working_days_type = $lastOrder['Order']['working_days_type'];

        $period_from = $this->request->data['period_from'];
        $period_to = $this->request->data['period_to'];
        $credit_release_date = $this->request->data['credit_release_date'];

        $orderData = [
            'customer_id' => $lastOrder['Order']['customer_id'],
            'economic_group_id' => $lastOrder['Order']['economic_group_id'],
            'working_days' => $working_days,
            'user_creator_id' => CakeSession::read("Auth.User.id"),
            'order_period_from' => $period_from,
            'order_period_to' => $period_to,
            'status_id' => 83,
            'is_partial' => $is_partial,
            'credit_release_date' => $credit_release_date,
            'created_at' => date('Y-m-d H:i:s'),
            'working_days_type' => $working_days_type,
            'benefit_type' => $benefit_type,
            'due_date' => $this->request->data['due_date'],
        ];

        $this->Order->create();
        $this->Order->save($orderData);
        $newId = $this->Order->id;

        $newItem = [];
        foreach ($lastOrder['OrderItem'] as $item) {
            unset($item['id']);
            unset($item['created']);
            unset($item['user_creator_id']);
            unset($item['updated']);
            unset($item['updated_user_id']);
            unset($item['transfer_fee_not_formated']);
            unset($item['commission_fee_not_formated']);
            unset($item['var_not_formated']);
            unset($item['price_per_day_not_formated']);
            unset($item['subtotal_not_formated']);
            unset($item['total_not_formated']);
            unset($item['saldo_not_formated']);
            unset($item['total_saldo_not_formated']);
            unset($item['data_inicio_processamento_nao_formatado']);
            unset($item['data_fim_processamento_nao_formatado']);

            $item['order_id'] = $newId;
            $item['data_inicio_processamento'] = null;
            $item['data_fim_processamento'] = null;
            $item['status_processamento'] = null;
            $item['motivo_processamento'] = null;
            $item['pedido_operadora'] = null;
            $item['user_creator_id'] = CakeSession::read("Auth.User.id");
            $newItem[] = $item;
        }

        $this->OrderItem->saveMany($newItem);
        $this->Order->reProcessAmounts($newId);
        $this->Order->reprocessFirstOrder($newId);

        $this->Flash->set(__('Pedido clonado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $newId]);
    }

    public function processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type, $proposal, $manualPricing = [])
    {
        $totalTransferFee = 0;
        $totalSubtotal = 0;
        $totalOrder = 0;

        foreach ($customerItineraries as $itinerary) {
            $currentUserId = $itinerary['CustomerUserItinerary']['customer_user_id'];

            // Skip if no manual pricing exists for this user
            if (!empty($manualPricing) && !isset($manualPricing[$currentUserId])) {
                continue;
            }

            // If manual pricing exists, create multiple order items
            if (!empty($manualPricing[$currentUserId])) {
                foreach ($manualPricing[$currentUserId] as $manualEntry) {
                    $parsedManualRow = $this->parseManualRow($itinerary, $manualEntry);

                    if (isset($manualEntry['idItinerary']) && $manualEntry['idItinerary'] != $itinerary['CustomerUserItinerary']['id']) {
                        continue;
                    }

                    if ($parsedManualRow === false) {
                        continue;
                    }

                    $pricePerDay = abs($parsedManualRow['pricePerDay']);
                    $manualWorkingDays = abs($parsedManualRow['manualWorkingDays']);
                    $manualQuantity = abs($parsedManualRow['manualQuantity']);

                    $itinerary['CustomerUserItinerary']['price_per_day_not_formated'] = $pricePerDay;

                    $this->createOrderItem(
                        $itinerary,
                        $orderId,
                        $workingDays,
                        $period_from,
                        $period_to,
                        $working_days_type,
                        $proposal,
                        $pricePerDay,
                        $manualWorkingDays,
                        $manualQuantity,
                        1, // values_from_csv
                        $totalTransferFee,
                        $totalSubtotal,
                        $totalOrder
                    );
                }
            } else {
                // Create single order item without manual pricing
                $this->createOrderItem(
                    $itinerary,
                    $orderId,
                    $workingDays,
                    $period_from,
                    $period_to,
                    $working_days_type,
                    $proposal,
                    $itinerary['CustomerUserItinerary']['price_per_day_not_formated'],
                    0, // manualWorkingDays
                    $itinerary['CustomerUserItinerary']['quantity'],
                    0, // values_from_csv
                    $totalTransferFee,
                    $totalSubtotal,
                    $totalOrder
                );
            }
        }
    }

    private function parseManualRow($itinerary, $row)
    {
        $isNewCsv = isset($row['newCsv']) ? $row['newCsv'] : false;
        $benefitIdOrCode = $isNewCsv ? $itinerary['Benefit']['id'] : $itinerary['Benefit']['code'];
        $benefitIdOrCode = (int)$benefitIdOrCode;
        $row['benefitId'] = (int)$row['benefitId'];

        if ($row['benefitId'] == $benefitIdOrCode) {
            $manualUnitPrice = $row['unitPrice'];
            $manualWorkingDays = (int)$row['workingDays'];
            $manualQuantity = $row['quantity'];

            $pricePerDay = $manualUnitPrice * $manualQuantity;

            return [
                'pricePerDay' => $pricePerDay,
                'manualWorkingDays' => $manualWorkingDays,
                'manualQuantity' => $manualQuantity
            ];
        }

        return false;
    }

    private function createOrderItem($itinerary, $orderId, $workingDays, $period_from, $period_to, $working_days_type, $proposal, $pricePerDay, $manualWorkingDays, $manualQuantity, $values_from_csv, &$totalTransferFee, &$totalSubtotal, &$totalOrder)
    {
        $commissionFee = 0;
        $commissionPerc = $this->getCommissionPerc($itinerary['Benefit']['benefit_type_id'], $proposal);
        $vacationDays = $this->CustomerUserVacation->getVacationsDays($itinerary['CustomerUserItinerary']['customer_user_id'], $period_from, $period_to);

        if ($working_days_type == 2) {
            $workingDays = $itinerary['CustomerUserItinerary']['working_days'];
        }

        $workingDaysUser = $workingDays - $vacationDays;

        if ($workingDaysUser < 0) {
            $workingDaysUser = 0;
        }

        if ($manualWorkingDays != 0) {
            $workingDaysUser = $manualWorkingDays;
        }

        $subtotal = $workingDaysUser * $pricePerDay;

        $benefitId = $itinerary['CustomerUserItinerary']['benefit_id'];
        $benefit = $this->Benefit->findById($benefitId);
        $transferFeePercentage = isset($benefit['Supplier']['transfer_fee_percentage_nao_formatado'])
            ? $benefit['Supplier']['transfer_fee_percentage_nao_formatado']
            : 0;

        // 1 = 'Valor', 2 = 'Percentual'
        if ($benefit['Supplier']['transfer_fee_type'] == 2) {
            $transferFee = $subtotal * ($transferFeePercentage / 100);
        } else {
            $transferFee = $transferFeePercentage;
        }

        $commissionFee = $commissionPerc > 0 ? $subtotal * ($commissionPerc / 100) : 0;

        $total = $subtotal + $transferFee + $commissionFee;

        $totalTransferFee += $transferFee;
        $totalSubtotal += $subtotal;
        $totalOrder += $total;

        $orderItemData = [
            'order_id' => $orderId,
            'status_processamento' => 'INICIO_PROCESSAMENTO',
            'data_inicio_processamento' => date('Y-m-d'),
            'customer_user_itinerary_id' => $itinerary['CustomerUserItinerary']['id'],
            'customer_user_id' => $itinerary['CustomerUserItinerary']['customer_user_id'],
            'working_days' => $workingDaysUser,
            'price_per_day' => $pricePerDay,
            'subtotal' => $subtotal,
            'transfer_fee' => $transferFee,
            'total' => $total,
            'commission_fee' => $commissionFee,
            'values_from_csv' => $values_from_csv,
            'manual_quantity' => $manualQuantity,
        ];

        $this->OrderItem->create();
        $this->OrderItem->save($orderItemData);
    }

    // private function parseManualRow($itinerary, $manualPricing)
    // {
    //     foreach ($manualPricing as $row) {
    //         $isNewCsv = isset($row['newCsv']) ? $row['newCsv'] : false;
    //         $benefitIdOrCode = $isNewCsv ? $itinerary['Benefit']['id'] : $itinerary['Benefit']['code'];
    //         $benefitIdOrCode = (int)$benefitIdOrCode;
    //         $row['benefitId'] = (int)$row['benefitId'];

    //         if ($row['benefitId'] == $benefitIdOrCode) {
    //             $manualUnitPrice = $row['unitPrice'];
    //             $manualWorkingDays = (int)$row['workingDays'];
    //             $manualQuantity = $row['quantity'];

    //             $pricePerDay = $manualUnitPrice * $manualQuantity;

    //             return ['pricePerDay' => $pricePerDay, 'manualWorkingDays' => $manualWorkingDays, 'manualQuantity' => $manualQuantity];
    //         }
    //     }

    //     return false;
    // }

    public function edit($id = null)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Order->id = $id;
        $old_order = $this->Order->read();
        $user = $this->Auth->user();
        $this->set('user', $user);

        $next_order = $this->Order->find('first', [
            'conditions' => ['Order.id >' => $id],
            'order' => ['Order.id' => 'ASC'],
            'fields' => ['Order.id']
        ]);

        $prev_order = $this->Order->find('first', [
            'conditions' => ['Order.id <' => $id],
            'order' => ['Order.id' => 'DESC'],
            'fields' => ['Order.id']
        ]);

        if ($this->request->is(['post', 'put'])) {
            $order = ['Order' => []];
            $order['Order']['id'] = $id;
            $order['Order']['gera_nfse'] = $this->request->data['Order']['gera_nfse'];
            $order['Order']['observation'] = $this->request->data['Order']['observation'];
            $order['Order']['nfse_observation'] = $this->request->data['Order']['nfse_observation'];
            $order['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");

            if (isset($this->request->data['Order']['pedido_complementar'])) {
                $order['Order']['pedido_complementar'] = $this->request->data['Order']['pedido_complementar'];
            }

            if (isset($this->request->data['Order']['observation_ge'])) {
                if (!empty($this->request->data['Order']['observation_ge'])) {
                    $order['Order']['observation_ge'] = $this->request->data['Order']['observation_ge'];
                    $order['Order']['updated_ge'] = date('Y-m-d H:i:s');
                    $order['Order']['user_updated_ge_id'] = CakeSession::read("Auth.User.id");
                }
            }

            if ($old_order['Order']['status_id'] < 85) {
                if ($old_order['Order']['desconto'] > 0 && $this->request->data['Order']['desconto'] == '') {
                    $total = ($old_order['Order']['transfer_fee_not_formated'] + $old_order['Order']['commission_fee_not_formated'] + $old_order['Order']['subtotal_not_formated']) + isset($old_order['Order']['desconto_not_formated']);
                } else {
                    $total = ($old_order['Order']['transfer_fee_not_formated'] + $old_order['Order']['commission_fee_not_formated'] + $old_order['Order']['subtotal_not_formated']) - $this->priceFormatBeforeSave($this->request->data['Order']['desconto']);
                }

                $order['Order']['desconto'] = $this->request->data['Order']['desconto'];
                $order['Order']['total'] = $total;
                $order['Order']['due_date'] = $this->request->data['Order']['due_date'];
            }

            if (!empty($this->request->data['Order']['end_date'])) {
                $order['Order']['status_id'] = 87;
                $order['Order']['end_date'] = $this->request->data['Order']['end_date'];
            }

            if ($this->Order->save($order)) {
                $this->Order->reProcessAmounts($id);
                $this->Order->reprocessFirstOrder($id);
                $this->Flash->set(__('O Pedido foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'edit/' . $id]);
            } else {
                $this->Flash->set(__('O Pedido não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Order->validationErrors;
        $this->request->data = $this->Order->read();
        $order = $this->Order->findById($id);
        $this->Order->validationErrors = $temp_errors;

        $this->Paginator->settings = ['OrderItem' => [
            'limit' => 100,
            'order' => ['CustomerUser.name' => 'asc'],
            'fields' => ['OrderItem.*', 'CustomerUserItinerary.*', 'Benefit.*', 'Order.*', 'CustomerUser.*'],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ]
            ]
        ]];

        $condition = ["and" => ['Order.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $_GET['q'] . "%", 'CustomerUser.cpf LIKE' => "%" . $_GET['q'] . "%", 'Benefit.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $items = $this->Paginator->paginate('OrderItem', $condition);

        $progress = 1;
        switch ($order['Order']['status_id']) {
            case 83:
                $progress = 1;
                break;

            case 84:
                $progress = 3;
                break;

            case 85:
                $progress = 5;
                break;

            case 86:
                $progress = 7;
                break;

            case 104:
                $progress = 8;
                break;

            case 87:
                $progress = 9;
                break;
        }

        $suppliersCount = $this->OrderItem->find('count', [
            'conditions' => ['OrderItem.order_id' => $id],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ]
            ],
            'group' => ['Supplier.id'],
            'fields' => ['Supplier.id']
        ]);

        $usersCount = $this->OrderItem->find('count', [
            'conditions' => ['OrderItem.order_id' => $id],
            'group' => ['OrderItem.customer_user_id'],
            'fields' => ['OrderItem.customer_user_id']
        ]);

        $income = $this->Income->find('first', [
            'conditions' => ['Income.order_id' => $id]
        ]);

        $benefits = $this->Benefit->find('list', ['fields' => ['id', 'complete_name'], 'order' => ['cast(Benefit.code as unsigned)' => 'asc']]);

        $gerarNota = $this->Permission->check(66, "leitura");

        $benefit_type_desc = 'Todos';
        if ($order['Order']['benefit_type'] != 0) {
            if ($order['Order']['benefit_type'] == -1) {
                $benefit_type_desc = 'Transporte';
            } else {
                $benefit_types = $this->BenefitType->find('first', [
                    'conditions' => ['BenefitType.id' => $order['Order']['benefit_type']]
                ]);

                $benefit_type_desc = isset($benefit_types['BenefitType']) ? $benefit_types['BenefitType']['name'] : '';
            }
        }

        $v_is_partial = "";
        if ($order['Order']['is_partial'] == 1) {
            $v_is_partial = "Importação";
        } elseif ($order['Order']['is_partial'] == 2) {
            $v_is_partial = "Automático";
        } elseif ($order['Order']['is_partial'] == 3) {
            $v_is_partial = "PIX";
        } elseif ($order['Order']['is_partial'] == 4) {
            $v_is_partial = "Emissão";
        }

        $order_balances_total = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $id, "OrderBalance.tipo" => 1], 'fields' => 'SUM(OrderBalance.total) as total']);

        $this->Order->recursive = 0;
        $orders = $this->Order->find(
            'all',
            [
                'fields' => [
                    'Order.*',
                    'OrderDiscount.id',
                    'Customer.nome_primario'
                ],
                'joins' => [
                    [
                        'table' => 'order_discounts',
                        'alias' => 'OrderDiscount',
                        'type' => 'LEFT',
                        'conditions' => [
                            'OrderDiscount.order_parent_id = Order.id',
                            'OrderDiscount.data_cancel' => '1901-01-01',
                        ]
                    ]
                ],
                'conditions' => [
                    'Order.id !=' => $id,
                    'Order.customer_id' => $order['Order']['customer_id']
                ],
                'order' => [
                    'Order.id' => 'DESC'
                ],
            ]
        );

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => '', 'Alterar Pedido' => ''];

        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items', 'progress', 'v_is_partial'));
        $this->set(compact('suppliersCount', 'usersCount', 'income', 'benefits', 'gerarNota', 'benefit_type_desc', 'order_balances_total', 'next_order', 'prev_order', 'orders'));

        $this->render("add");
    }

    // ajax function listOfCustomerUsers
    public function listOfCustomerUsers()
    {
        $this->autoRender = false;

        $customerId = $_GET['customer_id'];
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $customerUsers = $this->CustomerUser->find('list', [
            'conditions' => ['CustomerUser.customer_id' => $customerId, 'CustomerUser.name LIKE' => '%' . $search . '%', 'CustomerUser.status_id' => 1],
            'fields' => ['id', 'name'],
            'order' => ['name' => 'asc']
        ]);

        $cst_u = [];
        foreach ($customerUsers as $k => $user) {
            $cst_u[] = ['id' => $k, 'text' => $user];
        }

        echo json_encode(['results' => $cst_u, 'pagination' => ['more' => false]]);
    }

    public function changeStatusToSent($id)
    {
        $this->autoRender = false;

        $order = $this->Order->find('first', [
            'contain' => ['Customer'],
            'conditions' => ['Order.id' => $id],
            'recursive' => -1
        ]);

        $bancoEmissao = $this->BancoPadrao->find('first');

        $account = $this->BankAccount->find('first', [
            'conditions' => [
                'BankAccount.status_id' => 1,
                'BankAccount.bank_id' => $bancoEmissao['BancoPadrao']['bank_id'], // 1 para itau e 9 para btg
            ]
        ]);

        $this->Income->updateAll(
            ['Income.data_cancel' => 'CURRENT_DATE', 'Income.usuario_id_cancel' => CakeSession::read("Auth.User.id")],
            ['Income.order_id' => $id]
        );

        $income = [];

        $income['Income']['order_id'] = $id;
        $income['Income']['parcela'] = 1;
        $income['Income']['status_id'] = 15;
        $income['Income']['revenue_id'] = 1;
        $income['Income']['cost_center_id'] = 5;
        $income['Income']['payment_method'] = 1;
        $income['Income']['bank_account_id'] = $account['BankAccount']['id'];
        $income['Income']['customer_id'] = $order['Order']['customer_id'];
        $income['Income']['name'] = 'Conta a receber - Pedido ' . $order['Order']['id'];
        $income['Income']['valor_multa'] = 0;
        $income['Income']['valor_bruto'] = $order['Order']['total'];
        $income['Income']['valor_total'] = $order['Order']['total'];
        $income['Income']['vencimento'] = $order['Order']['due_date'];
        $income['Income']['data_competencia'] = date('01/m/Y');
        $income['Income']['user_creator_id'] = CakeSession::read("Auth.User.id");

        $this->Income->create();
        $this->Income->save($income);

        if ($this->emitirBoleto($this->Income->id)) {
            if ($order['Customer']['emitir_nota_fiscal'] == 'A') {
                $this->notificaNotaAntecipada($order);
            }

            $this->Order->save([
                'Order' => [
                    'id' => $id,
                    'status_id' => 84,
                    'user_updated_id' => CakeSession::read("Auth.User.id"),
                    'validation_date' => date('Y-m-d'),
                ]
            ]);

            $this->Flash->set(__('O Pedido enviado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } else {
            $this->Income->deleteAll(['Income.id' => $this->Income->id], false);
        }

        $this->redirect(['action' => 'edit/' . $id]);
    }

    public function notificaNotaAntecipada($order)
    {
        $dados = [
            'viewVars' => [
                'nome' => 'Financeiro',
                'email' => 'rodolfo.note@gmail.com',
                'pedido' => $order['Order']['id'],
            ],
            'template' => 'nota_fiscal_antecipada',
            'subject' => 'BeRH - Nota Fiscal',
            'config' => 'default',
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => 'alert alert-danger']]);
        }

        $dados = [
            'viewVars' => [
                'nome' => 'Financeiro',
                'email' => 'financeiro@berh.com.br',
                'pedido' => $order['Order']['id'],
            ],
            'template' => 'nota_fiscal_antecipada',
            'subject' => 'BeRH - Nota Fiscal',
            'config' => 'default',
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => 'alert alert-danger']]);
        }
    }

    public function emitirBoleto($id)
    {
        $conta = $this->Income->find('first', [
            'conditions' => ['Income.id' => $id],
            'recursive' => -1,
            'fields' => ['Income.*', 'Customer.*', 'BankAccount.*', 'BankTicket.*', 'Order.id', 'Order.economic_group_id'],
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'inner',
                    'conditions' => [
                        'Customer.id = Income.customer_id',
                        'Customer.data_cancel' => '1901-01-01',
                    ],
                ],
                [
                    'table' => 'bank_accounts',
                    'alias' => 'BankAccount',
                    'type' => 'inner',
                    'conditions' => [
                        'BankAccount.id = Income.bank_account_id',
                        'BankAccount.data_cancel' => '1901-01-01',
                    ],
                ],
                [
                    'table' => 'bank_tickets',
                    'alias' => 'BankTicket',
                    'type' => 'inner',
                    'conditions' => [
                        'BankAccount.id = BankTicket.bank_account_id',
                        'BankTicket.data_cancel' => '1901-01-01',
                    ],
                ],
                [
                    'table' => 'orders',
                    'alias' => 'Order',
                    'type' => 'inner',
                    'conditions' => [
                        'Order.id = Income.order_id'
                    ],
                ],
            ],
        ]);

        if (!empty($conta)) {
            $remessas = $this->CnabLote->find('first', ['order' => ['CnabLote.id' => 'desc'], 'callbacks' => false]);
            $remessa = isset($remessas['CnabLote']) ? $remessas['CnabLote']['remessa'] + 1 : 1;

            $nome_arquivo = 'E' . $this->zerosEsq($remessa, 6) . '.REM';

            $data_pefin_lote = [
                'CnabLote' => [
                    'status_id' => 46,
                    'arquivo' => $nome_arquivo,
                    'remessa' => $remessa,
                    'bank_id' => 1,
                    'user_creator_id' => CakeSession::read("Auth.User.id"),
                ],
            ];

            $this->CnabLote->create();
            $this->CnabLote->save($data_pefin_lote);

            if ($conta['BankAccount']['bank_id'] == 9) {
                $ApiBtgPactual = new ApiBtgPactual();
                $boleto = $ApiBtgPactual->gerarBoleto($conta);
            } else {
                $ApiItau = new ApiItau();
                $boleto = $ApiItau->gerarBoleto($conta);
            }

            if ($boleto['success']) {
                $idWeb = $conta['BankAccount']['bank_id'] == 9 ? $boleto['contents']['bankSlipId'] : $boleto['contents']['data']['dado_boleto']['dados_individuais_boleto'][0]['numero_nosso_numero'];
                $this->CnabItem->create();
                $this->CnabItem->save([
                    'CnabItem' => [
                        'cnab_lote_id' => $this->CnabLote->id,
                        'income_id' => $conta['Income']['id'],
                        'id_web' => $idWeb,
                        'status_id' => 48,
                        'user_creator_id' => CakeSession::read("Auth.User.id"),
                    ],
                ]);

                $this->Income->id = $conta['Income']['id'];
                $this->Income->save([
                    'Income' => [
                        'cnab_gerado' => 1,
                        'status_id' => 16,
                        'cnab_lote_id' => $this->CnabLote->id,
                        'user_updated_id' => CakeSession::read("Auth.User.id"),
                    ],
                ]);

                return true;
            } else {
                $erros = $boleto['error'];
                if (!is_array($boleto['error'])) {
                    $erros = [$boleto['error']];
                }

                $message = '';
                foreach ($erros as $erro) {
                    $message .= $erro . '<br>';
                }

                $this->Income->deleteAll(['Income.id' => $id], false);

                $this->Flash->set(__($message), ['element' => 'flash', 'params' => ['class' => "alert alert-danger"]]);
            }
        }

        return false;
    }

    public function changeStatusIssued($id)
    {
        $this->autoRender = false;

        $order = ['Order' => []];
        $order['Order']['id'] = $id;
        $order['Order']['status_id'] = 86;
        $order['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");
        $order['Order']['issuing_date'] = date('Y-m-d');

        if ($this->Order->save($order)) {
            $this->Flash->set(__('O Pedido enviado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'edit/' . $id]);
        } else {
            $this->Flash->set(__('O Pedido não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
        }
    }

    public function addCustomerUserToOrder()
    {
        $orderId = $this->request->data['order_id'];
        $customerUserId = $this->request->data['customer_user_id'];
        $workingDays = $this->request->data['working_days'];

        $order = $this->Order->findById($orderId);
        $cond = [
            'CustomerUserItinerary.customer_user_id' => $customerUserId, 
            'CustomerUserItinerary.status_id' => 1,
            'Benefit.status_id' => 1,
            'Supplier.status_id' => 1,
        ];

        $proposal = $this->Proposal->find('first', [
            'conditions' => ['Proposal.customer_id' => $order['Order']['customer_id'], 'Proposal.status_id' => 99]
        ]);
        if (empty($proposal)) {
            $this->Flash->set(__('Cliente não possui uma proposta ativa.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        if ($order['Order']['benefit_type'] != 0) {
            $benefit_type = $order['Order']['benefit_type'];
            $benefit_type = $this->groupBenefitType[$benefit_type];
            $cond['Benefit.benefit_type_id'] = $benefit_type;
        }

        $orderItems = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $orderId, 'OrderItem.customer_user_id' => $customerUserId],
        ]);

        if (!empty($orderItems)) {
            $arr_itineraries = Hash::extract($orderItems, '{n}.OrderItem.customer_user_itinerary_id');
            $cond['CustomerUserItinerary.id NOT IN'] = $arr_itineraries;
        }

        $customerItineraries = $this->CustomerUserItinerary->find('all', [
            'conditions' => $cond,
            'joins' => [
            [
                'table' => 'suppliers',
                'alias' => 'Supplier',
                'type' => 'INNER',
                'conditions' => [
                'Supplier.id = Benefit.supplier_id'
                ]
            ]
            ]
        ]);

        $this->processItineraries($customerItineraries, $orderId, $workingDays, $order['Order']['order_period_from'], $order['Order']['order_period_to'], 1, $proposal);

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);
        $this->Order->reprocessFirstOrder($orderId);

        $this->Flash->set(__('Beneficiário incluído com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $orderId]);
    }

    public function upload_user_csv()
    {
        $orderId = $this->request->data['order_id'];
        $customerId = $this->request->data['customer_id'];

        $proposal = $this->Proposal->find('first', [
            'conditions' => ['Proposal.customer_id' => $customerId, 'Proposal.status_id' => 99]
        ]);
        if (empty($proposal)) {
            $this->Flash->set(__('Cliente não possui uma proposta ativa.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        $file = $this->request->data['CustomerUserItinerary'];
        $tipo_importacao = (int)$this->request->data['tipo_importacao'];

        if ($tipo_importacao == 2) {
            $ret = $this->parseCSVwithCPFColumn($customerId, $file['file']['tmp_name']);
        } else {
            $ret = $this->parseNewCsv($customerId, $file['file']['tmp_name']);
        }

        if (isset($ret['error'])) {
            $this->Flash->error($ret['error'], ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        $customerUsersIds = $ret['customerUsersIds'];
        $manualPricing = $ret['unitPriceMapping'];

        $order = $this->Order->findById($orderId);
        $cond = [
            'CustomerUserItinerary.customer_user_id' => $customerUsersIds, 
            'CustomerUserItinerary.status_id' => 1,
            'Benefit.status_id' => 1,
            'Supplier.status_id' => 1,
        ];

        if ($order['Order']['benefit_type'] != 0) {
            $benefit_type = $order['Order']['benefit_type'];
            $benefit_type = $this->groupBenefitType[$benefit_type];
            $cond['Benefit.benefit_type_id'] = $benefit_type;
        }

        $customerItineraries = $this->CustomerUserItinerary->find('all', [
            'conditions' => $cond,
            'joins' => [
            [
                'table' => 'suppliers',
                'alias' => 'Supplier',
                'type' => 'INNER',
                'conditions' => [
                'Supplier.id = Benefit.supplier_id'
                ]
            ]
            ]
        ]);

        $this->processItineraries($customerItineraries, $orderId, $order['Order']['working_days'], $order['Order']['order_period_from'], $order['Order']['order_period_to'], $order['Order']['working_days_type'], $proposal, $manualPricing);

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);
        $this->Order->reprocessFirstOrder($orderId);

        $this->Flash->set(__('Beneficiário(s) incluído(s) com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $orderId]);
    }

    public function upload_saldo_csv()
    {
        $orderId = $this->request->data['order_id'];
        $customerId = $this->request->data['customer_id'];

        $ret = $this->parseCSVSaldo($customerId, $orderId, $this->request->data['file']['tmp_name']);

        $groupTipoItens = [];

        foreach ($ret['data'] as $data) {
            $groupTipoItens[$data['tipo']][] = $data['order_item_id'];
        }

        foreach ($groupTipoItens as $tipo => $itens) {
            if ($tipo) {
                foreach ($itens as $itemId) {
                    $this->OrderBalance->update_cancel_balances($orderId, $tipo, CakeSession::read("Auth.User.id"), $itemId);
                }
            }
        }

        foreach ($ret['data'] as $data) {
            if ($data['tipo']) {
                $benefit = $this->Benefit->find('first', ['conditions' => ['Benefit.code' => $data['benefit_code']]]);

                if (isset($benefit['Benefit'])) {
                    $benefit_id = $benefit['Benefit']['id'];
                } else {
                    $benefit_id = null;
                }

                $orderBalanceData = [
                    'order_id' => $orderId,
                    'order_item_id' => $data['order_item_id'],
                    'customer_user_id' => $data['customer_user_id'],
                    'benefit_id' => $benefit_id,
                    'document' => $data['document'],
                    'total' => $data['total'],
                    'pedido_operadora' => $data['pedido_operadora'],
                    'tipo' => $data['tipo'],
                    'observacao' => $data['observacao'],
                    'created' => date('Y-m-d H:i:s'),
                    'user_created_id' => CakeSession::read("Auth.User.id")
                ];

                $this->OrderBalance->create();
                $this->OrderBalance->save($orderBalanceData);
            }
        }

        $this->OrderBalance->update_order_item_saldo($orderId, CakeSession::read("Auth.User.id"));

        $file = new File($this->request->data['file']['name']);
        $dir = new Folder(APP . "webroot/files/order_balances/" . $orderId . "/", true);

        $this->Uploader->up($this->request->data['file'], $dir->path);

        $this->Flash->set(__('Saldos incluídos com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'saldos/' . $orderId]);
    }

    private function parseCSVwithCPFColumn($customerId, $tmpFile)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $has_valor_unitario_invalido = false;
        $line = 0;
        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                $line++;
                continue;
            }

            $cpfToValidate = $row[0];

            if (empty($cpfToValidate) || !$this->isValidCPF($cpfToValidate)) {
                return ['success' => false, 'error' => 'CPF inválido na linha ' . ($line + 1) . '.'];
            }

            if (count($row) > 5) {
                return ['success' => false, 'error' => 'Arquivo inválido.'];
            }

            $benefitId = $row[3];
            $unitPrice = $row[1];

            $benefit = $this->Benefit->find('first', [
                'conditions' => [
                    'Benefit.code' => $benefitId,
                    'Benefit.data_cancel' => '1901-01-01 00:00:00'
                ],
                'fields' => ['Benefit.id', 'Benefit.is_variable']
            ]);

            if (empty($benefit)) {
                $line++;
                continue; // Skip if no benefit is found
            }

            $is_variable = (int)$benefit['Benefit']['is_variable'] === 1;

            if (empty($unitPrice) && $is_variable) {
                $has_valor_unitario_invalido = true;
                break;
            }
            $unitPrice = str_replace(".", "", $unitPrice);
            $unitPrice = (float)str_replace(",", ".", $unitPrice);

            if ($unitPrice <= 0 && $is_variable) {
                $has_valor_unitario_invalido = true;
                break;
            }
        }

        if ($has_valor_unitario_invalido) {
            return ['success' => false, 'error' => 'Favor verificar os valores unitários do arquivo.'];
        }

        $line = 0;
        $customerUsersIds = [];
        $unitPrice = 0;
        $unitPriceMapping = [];
        foreach ($csv->getRecords() as $row) {
            $unitPrice = 0;
            $workingDays = 0;
            $quantity = 0;
            $benefitId = 0;
            if ($line == 0 || empty($row[0])) {
                if ($line == 0) {
                    $line++;
                }
                continue;
            }

            $cpf = $this->ensureLeadingZeroes($row[0]);

            $existingUser = $this->CustomerUser->find('first', [
                'conditions' => [
                    "REPLACE(REPLACE(CustomerUser.cpf, '-', ''), '.', '')" => $cpf,
                    'CustomerUser.customer_id' => $customerId,
                ]
            ]);

            if (empty($existingUser)) {
                $line++;
                continue;
            }

            $benefitId = $row[3];

            $benefit = $this->Benefit->find('first', [
                'conditions' => [
                    'Benefit.code' => $benefitId,
                    'Benefit.data_cancel' => '1901-01-01 00:00:00'
                ],
                'fields' => ['Benefit.id', 'Benefit.is_variable']
            ]);

            // desabilita temporariamente a verificação de benefício
            // if ((int)$benefit['Benefit']['is_variable'] === 1) {
            $unitPrice = $row[1];
            // convert brl string to float
            $unitPrice = str_replace(".", "", $unitPrice);
            $unitPrice = (float)str_replace(",", ".", $unitPrice);
            $workingDays = $row[2];
            $benefitId = $row[3];
            $quantity = $row[4];
            $unitPriceMapping[$existingUser['CustomerUser']['id']][] = ['unitPrice' => $unitPrice, 'workingDays' => $workingDays, 'quantity' => $quantity, 'benefitId' => $benefitId];
            // }

            $customerUsersIds[] = $existingUser['CustomerUser']['id'];

            $line++;
        }

        return ['customerUsersIds' => $customerUsersIds, 'unitPriceMapping' => $unitPriceMapping];
    }

    private function parseCSVSaldo($customerId, $orderId, $tmpFile)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $line = 0;
        $data = [];
        foreach ($csv->getRecords() as $row) {
            $saldo = 0;

            if ($line == 0 || empty($row[0])) {
                if ($line == 0) {
                    $line++;
                }
                continue;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);

            $existingUser = $this->OrderBalance->find_user_order_items($orderId, $cpf);

            $customer_user_id = null;
            if (isset($existingUser[0]['u'])) {
                $customer_user_id = $existingUser[0]['u']['id'];
            }

            $total = str_replace("R$", "", $row[2]);
            $total = str_replace(" ", "", $total);

            $data[] = [
                'customer_user_id' => $customer_user_id,
                'document' => $row[0],
                'benefit_code' => $row[1],
                'total' => $total,
                'pedido_operadora' => $row[3],
                'order_item_id' => $row[4],
                'tipo' => $row[5],
                'observacao' => $row[6],
            ];

            $line++;
        }

        return ['data' => $data];
    }

    public function updateWorkingDays()
    {
        $this->autoRender = false;

        $itemId = $this->request->data['orderItemId'];

        $orderItem = $this->OrderItem->findById($itemId);

        if ($this->request->data['campo'] == 'working_days') {
            $workingDays = $this->request->data['newValue'];
            if ($workingDays < 0) {
                $workingDays = $workingDays * -1;
            }
            $orderItem['OrderItem']['working_days'] = $workingDays;
            $var = $orderItem['OrderItem']['var_not_formated'];
        } else {
            $workingDays = $orderItem['OrderItem']['working_days'];
            if ($workingDays < 0) {
                $workingDays = $workingDays * -1;
            }
            $var_raw = $this->request->data['newValue'];
            $var = str_replace(".", "", $var_raw);
            $var = (float)str_replace(",", ".", $var);
            $orderItem['OrderItem']['var'] = $var_raw;
        }
        $orderItem['OrderItem']['updated_user_id'] = CakeSession::read("Auth.User.id");
        $orderItem['OrderItem']['subtotal'] = $workingDays * $orderItem['OrderItem']['price_per_day_not_formated'];
        $orderItem['OrderItem']['subtotal'] = $orderItem['OrderItem']['subtotal'] - $var;

        $benefitId = $orderItem['CustomerUserItinerary']['benefit_id'];
        $benefit = $this->Benefit->findById($benefitId);

        $transferFeePercentage = $benefit['Supplier']['transfer_fee_percentage_nao_formatado'];
        // 1 = 'Valor', 2 = 'Percentual'
        if ($benefit['Supplier']['transfer_fee_type'] == 2) {
            $transferFee = $orderItem['OrderItem']['subtotal'] * ($transferFeePercentage / 100);
        } else {
            $transferFee = $transferFeePercentage;
        }

        $orderItem['OrderItem']['transfer_fee'] = $transferFee;

        $proposal = $this->Proposal->find('first', [
            'conditions' => ['Proposal.customer_id' => $orderItem['Order']['customer_id'], 'Proposal.status_id' => 99]
        ]);
        $commissionPerc = 0;
        if (!empty($proposal)) {
            $commissionPerc = $this->getCommissionPerc($benefit['Benefit']['benefit_type_id'], $proposal);
        }

        $orderItem['OrderItem']['commission_fee'] = $commissionPerc > 0 ? $orderItem['OrderItem']['subtotal'] * ($commissionPerc / 100) : 0;

        $orderItem['OrderItem']['total'] = $orderItem['OrderItem']['subtotal'] + $transferFee + $orderItem['OrderItem']['commission_fee'];

        $this->OrderItem->id = $itemId;
        $this->OrderItem->save($orderItem);

        $orderItem = $this->OrderItem->findById($itemId);

        $this->Order->id = $orderItem['OrderItem']['order_id'];
        $this->Order->reProcessAmounts($orderItem['OrderItem']['order_id']);
        $this->Order->reprocessFirstOrder($orderItem['OrderItem']['order_id']);

        $order = $this->Order->findById($orderItem['OrderItem']['order_id']);

        $pedido_subtotal = $order['Order']['subtotal'];
        $pedido_transfer_fee = $order['Order']['transfer_fee'];
        $pedido_commission_fee = $order['Order']['commission_fee'];
        $pedido_total = $order['Order']['total'];

        echo json_encode([
            'success' => true,
            'subtotal' => $orderItem['OrderItem']['subtotal'],
            'transfer_fee' => $orderItem['OrderItem']['transfer_fee'],
            'commission_fee' => $orderItem['OrderItem']['commission_fee'],
            'total' => $orderItem['OrderItem']['total'],
            'pedido_subtotal' => $pedido_subtotal,
            'pedido_transfer_fee' => $pedido_transfer_fee,
            'pedido_commission_fee' => $pedido_commission_fee,
            'pedido_total' => $pedido_total
        ]);
    }

    public function removeOrderItem($orderId = false, $itemOrderId = false)
    {
        $this->autoRender = false;

        $is_multiple = false;
        if ($orderId == false || $itemOrderId == false) {
            $is_multiple = true;
            $orderId = $this->request->data['orderId'];
            $itemOrderId = $this->request->data['orderItemIds'];
        }

        $this->OrderItem->unbindModel(
            ['belongsTo' => ['Order', 'CustomerUserItinerary', 'CustomerUser']]
        );

        $this->OrderItem->updateAll(
            ['OrderItem.data_cancel' => 'CURRENT_DATE', 'OrderItem.usuario_id_cancel' => CakeSession::read("Auth.User.id")],
            ['OrderItem.id' => $itemOrderId]
        );

        $this->OrderItem->bindModel(
            ['belongsTo' => ['Order', 'CustomerUserItinerary', 'CustomerUser']]
        );

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);
        $this->Order->reprocessFirstOrder($orderId);

        if ($is_multiple) {
            echo json_encode(['success' => true]);
        } else {
            $this->redirect('/orders/edit/' . $orderId);
        }
    }

    public function addItinerary()
    {
        $id = $this->request->data['customer_id'];
        $orderId = $this->request->data['order_id'];

        $proposal = $this->Proposal->find('first', [
            'conditions' => ['Proposal.customer_id' => $id, 'Proposal.status_id' => 99]
        ]);
        if (empty($proposal)) {
            $this->Flash->set(__('Cliente não possui uma proposta ativa.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        $this->CustomerUserItinerary->create();
        $this->CustomerUserItinerary->validates();

        $this->request->data['CustomerUserItinerary']['user_creator_id'] = CakeSession::read("Auth.User.id");
        $this->request->data['CustomerUserItinerary']['customer_id'] = $id;

        if ($this->CustomerUserItinerary->save($this->request->data)) {

            $idLastInserted = $this->CustomerUserItinerary->getLastInsertId();

            $order = $this->Order->findById($orderId);

            $customerItineraries = $this->CustomerUserItinerary->find('all', [
                'conditions' => [
                    'CustomerUserItinerary.id' => $idLastInserted,
                    'Benefit.status_id' => 1,
                    'Supplier.status_id' => 1,
                ],
                'joins' => [
                    [
                        'table' => 'suppliers',
                        'alias' => 'Supplier',
                        'type' => 'INNER',
                        'conditions' => [
                            'Supplier.id = Benefit.supplier_id'
                        ]
                    ]
                ]
            ]);

            $this->processItineraries($customerItineraries, $orderId, $order['Order']['working_days'], $order['Order']['order_period_from'], $order['Order']['order_period_to'], 1, $proposal);

            $this->Order->id = $orderId;
            $this->Order->reProcessAmounts($orderId);
            $this->Order->reprocessFirstOrder($orderId);

            $this->Flash->set(__('Itinerário adicionado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect('/orders/edit/' . $orderId);
        } else {
            $this->Flash->set(__('Itinerário não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
        }
    }

    private function processConsolidated($customerId, $workingDays, $period_from, $period_to, $is_partial, $credit_release_date, $working_days_type, $grupo_especifico, $benefit_type, $proposal, $pedido_complementar)
    {
        $cond = [
            'CustomerUserItinerary.customer_id' => $customerId,
            'CustomerUserItinerary.status_id' => 1,
            // 'CustomerUser.status_id' => 1,
            'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
        ];
        if ($grupo_especifico != '') {
            $cond['CustomerUserEconomicGroup.economic_group_id'] = $grupo_especifico;
        }

        $benefit_type_persist = 0;
        if ($benefit_type != '') {
            $benefit_type_persist = $benefit_type;
            $benefit_type = (int)$benefit_type;
            $benefit_type = $this->groupBenefitType[$benefit_type];
            $cond['Benefit.benefit_type_id'] = $benefit_type;
        }

        $economic_groups = $this->CustomerUserItinerary->find('all', [
            'conditions' => $cond,
            'joins' => [
                [
                    'table' => 'customer_users',
                    'alias' => 'CustomerUser',
                    'type' => 'INNER',
                    'conditions' => [
                        'CustomerUser.id = CustomerUserItinerary.customer_user_id'
                    ]
                ],
                [
                    'table' => 'customer_users_economic_groups',
                    'alias' => 'CustomerUserEconomicGroup',
                    'type' => 'LEFT',
                    'conditions' => [
                        'CustomerUser.id = CustomerUserEconomicGroup.customer_user_id'
                    ]
                ],
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ]
            ],
            'recursive' => -1,
            'group' => ['CustomerUser.id'],
            'fields' => ['CustomerUser.id', 'CustomerUserEconomicGroup.economic_group_id', 'COUNT(DISTINCT CustomerUserEconomicGroup.economic_group_id) as EconomicGroupCount']
        ]);

        if (empty($economic_groups)) {
            return false;
        }

        $economicGroupUsers = array();

        foreach ($economic_groups as $group) {
            $userId = $group['CustomerUser']['id'];
            $economicGroupCount = $group[0]['EconomicGroupCount'];
            $economicGroupId = $group['CustomerUserEconomicGroup']['economic_group_id'];

            if ($economicGroupCount == 1) {
                // User belongs to exactly one economic group
                if (!isset($economicGroupUsers[$economicGroupId])) {
                    $economicGroupUsers[$economicGroupId] = array();
                }
                $economicGroupUsers[$economicGroupId][] = $userId;
            } else {
                // User belongs to multiple or no economic groups
                $economicGroupUsers['NOK'][] = $userId;
            }
        }

        foreach ($economicGroupUsers as $k => $user_list) {
            $cond2 = [
                'CustomerUserItinerary.customer_id' => $customerId,
                'CustomerUserItinerary.status_id' => 1,
                'CustomerUser.status_id' => 1,
                'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
                'CustomerUser.id' => $user_list,
                'Benefit.status_id' => 1,
                'Supplier.status_id' => 1,
            ];

            if ($benefit_type != '') {
                $cond2['Benefit.benefit_type_id'] = $benefit_type;
            }

            if ($is_partial == 2) {
                $customerItineraries = $this->CustomerUserItinerary->find('all', [
                    'conditions' => $cond2,
                    'joins' => [
                        [
                            'table' => 'suppliers',
                            'alias' => 'Supplier',
                            'type' => 'INNER',
                            'conditions' => [
                                'Supplier.id = Benefit.supplier_id'
                            ]
                        ]
                    ]
                ]);

                if (empty($customerItineraries)) {
                    $this->Flash->set(__('Nenhum itinerário encontrado para este cliente.'), ['params' => ['class' => "alert alert-danger"]]);
                    $this->redirect(['action' => 'index']);
                }
            }

            if ($k == 'NOK') {
                $k = null;
            }

            $customer = $this->Customer->find('first', ['fields' => ['Customer.observacao_notafiscal', 'Customer.flag_gestao_economico', 'Customer.porcentagem_margem_seguranca', 'Customer.qtde_minina_diaria', 'Customer.tipo_ge'], 'conditions' => ['Customer.id' => $customerId], 'recursive' => -1]);

            $customer_orders = $this->Order->find('count', ['conditions' => ['Order.customer_id' => $customerId]]);

            $orderData = [
                'customer_id' => $customerId,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period_from' => $period_from,
                'order_period_to' => $period_to,
                'status_id' => 83,
                'credit_release_date' => $credit_release_date,
                'is_partial' => $is_partial,
                'pedido_complementar' => $pedido_complementar,
                'created' => date('Y-m-d H:i:s'),
                'economic_group_id' => $k,
                'working_days_type' => $working_days_type,
                'benefit_type' => $benefit_type_persist,
                'flag_gestao_economico' => $customer['Customer']['flag_gestao_economico'],
                'porcentagem_margem_seguranca' => $customer['Customer']['porcentagem_margem_seguranca'],
                'qtde_minina_diaria' => $customer['Customer']['qtde_minina_diaria'],
                'tipo_ge' => $customer['Customer']['tipo_ge'],
                'primeiro_pedido' => ($customer_orders > 1 ? "N" : "S"),
            ];

            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();

                if ($is_partial == 2) {
                    $this->processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type, $proposal);
                }

                $this->Order->id = $orderId;
                $this->Order->reProcessAmounts($orderId);
                $this->Order->reprocessFirstOrder($orderId);

                if ($k == 0) {
                    $this->Flash->set(__('Pedido gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
                }
            } else {
                if ($k == 0) {
                    $this->Flash->set(__('Falha ao criar pedido. Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            }
        }

        return $orderId;
    }

    public function getEconomicGroupAndBenefitByCustomer()
    {
        $this->autoRender = false;

        $customerId = $this->request->data['customer_id'];

        // find all customer user itineraries by customer
        $economic_groups = $this->CustomerUserItinerary->find('all', [
            'conditions' => [
                'CustomerUserItinerary.customer_id' => $customerId,
                'CustomerUserItinerary.status_id' => 1,
                'CustomerUser.status_id' => 1,
                'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
            ],
            'joins' => [
                [
                    'table' => 'customer_users',
                    'alias' => 'CustomerUser',
                    'type' => 'INNER',
                    'conditions' => [
                        'CustomerUser.id = CustomerUserItinerary.customer_user_id'
                    ]
                ],
                [
                    'table' => 'customer_users_economic_groups',
                    'alias' => 'CustomerUserEconomicGroup',
                    'type' => 'INNER',
                    'conditions' => [
                        'CustomerUser.id = CustomerUserEconomicGroup.customer_user_id'
                    ]
                ],
                [
                    'table' => 'economic_groups',
                    'alias' => 'EconomicGroup',
                    'type' => 'INNER',
                    'conditions' => [
                        'EconomicGroup.id = CustomerUserEconomicGroup.economic_group_id'
                    ]
                ]
            ],
            'recursive' => -1,
            'group' => ['CustomerUserEconomicGroup.economic_group_id'],
            'fields' => ['EconomicGroup.name', 'EconomicGroup.id']
        ]);

        echo json_encode(['economicGroups' => $economic_groups]);
    }

    public function getEconomicGroupByCustomer()
    {
        $this->autoRender = false;

        $customerId = $this->request->data['customer_id'];

        // find all customer user itineraries by customer
        $economic_groups = $this->EconomicGroup->find('all', [
            'conditions' => [
                'EconomicGroup.customer_id' => $customerId,
                'EconomicGroup.status_id' => 1,
            ],
            'fields' => ['EconomicGroup.name', 'EconomicGroup.id']
        ]);

        echo json_encode(['economicGroups' => $economic_groups]);
    }

    public function delete($id)
    {
        $this->Permission->check(63, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Order->id = $id;
        $this->request->data = $this->Order->read();

        $this->request->data['Order']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Order']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Order->save($this->request->data)) {
            $this->Flash->set(__('O Pedido foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function operadoras($id)
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $suppliersAll = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id],
            'fields' => [
                'Order.id',
                'Supplier.id',
                'Supplier.razao_social',
                'OrderItem.status_processamento',
                'sum(OrderItem.subtotal) as subtotal',
                "(SELECT sum(b.total) as total_saldo
                                FROM order_balances b
                                INNER JOIN benefits be ON be.id = b.benefit_id
                                WHERE be.supplier_id = Supplier.id
                                        AND b.tipo = 1
                                        AND b.order_id = OrderItem.order_id
                                        AND b.data_cancel = '1901-01-01 00:00:00'
                            ) AS total_saldo",
                "(SELECT max(b.pedido_operadora) as pedido_operadora
                                FROM order_balances b
                                WHERE b.benefit_id = Benefit.id
                                        AND b.tipo = 1
                                        AND b.order_id = OrderItem.order_id
                                        AND b.data_cancel = '1901-01-01 00:00:00'
                            ) AS pedido_operadora",
                "(SELECT COUNT(1)
                                FROM outcomes o
                                WHERE o.order_id = OrderItem.order_id
                                        AND o.supplier_id = Supplier.id
                                        AND o.data_cancel = '1901-01-01 00:00:00'
                            ) AS count_outcomes"
            ],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ],
            ],
            'group' => ['Supplier.id', 'OrderItem.status_processamento']

        ]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Operadores' => ''];
        $this->set(compact('action', 'breadcrumb', 'id', 'suppliersAll'));
    }

    public function operadoras_detalhes($id, $supplier_id)
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $suppliersAll = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id, 'Supplier.id' => $supplier_id],
            'fields' => [
                'Order.id',
                'Supplier.id',
                'Supplier.razao_social',
                'Benefit.name',
                'CustomerUser.name',
                'OrderItem.*',
            ],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ],
            ],
            'group' => ['OrderItem.id']

        ]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Operadores' => '', 'Detalhes' => ''];
        $this->set(compact('action', 'breadcrumb', 'id', 'suppliersAll'));
    }

    public function confirma_pagamento($id)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;

        $this->Order->recursive = -1;
        $order = $this->Order->findById($id);

        $this->Order->save([
            'Order' => [
                'id' => $id,
                'status_id' => 85,
                'user_updated_id' => CakeSession::read("Auth.User.id"),
                'validation_date' => date('Y-m-d'),
            ]
        ]);

        $this->Flash->set(__('O Pagamento foi confirmado com sucesso'), ['params' => ['class' => "alert alert-success"]]);

        $this->redirect(['action' => 'edit/' . $id]);
    }


    public function gerar_pagamento()
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;

        $id = $this->request->data['orderId'];
        $supplier_id = $this->request->data['suppliersIds'];

        $suppliersAll = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id, 'Supplier.id' => $supplier_id],
            'fields' => [
                'Supplier.id',
                'round(sum(OrderItem.subtotal),2) as subtotal',
                "(SELECT round(sum(b.total),2) as total_saldo
                                FROM order_balances b
                                INNER JOIN benefits be ON be.id = b.benefit_id
                                WHERE be.supplier_id = Supplier.id
                                        AND b.tipo = 1
                                        AND b.order_id = OrderItem.order_id
                                        AND b.data_cancel = '1901-01-01 00:00:00'
                            ) AS total_saldo",
            ],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ]
            ],
            'group' => ['Supplier.id']
        ]);

        foreach ($suppliersAll as $supplier) {
            $outcome = [];

            $valor_total = ($supplier[0]['subtotal'] - $supplier[0]['total_saldo']);

            $outcome['Outcome']['order_id'] = $id;
            $outcome['Outcome']['parcela'] = 1;
            $outcome['Outcome']['status_id'] = 11;
            $outcome['Outcome']['bank_account_id'] = 3;
            $outcome['Outcome']['plano_contas_id'] = 3;
            $outcome['Outcome']['resale_id'] = 1;
            $outcome['Outcome']['cost_center_id'] = 5;
            $outcome['Outcome']['supplier_id'] = $supplier['Supplier']['id'];
            $outcome['Outcome']['name'] = 'Pagamento Fornecedor';
            $outcome['Outcome']['valor_multa'] = 0;
            $outcome['Outcome']['valor_bruto'] = number_format($valor_total, 2, ',', '.');
            $outcome['Outcome']['valor_total'] = number_format($valor_total, 2, ',', '.');
            $outcome['Outcome']['vencimento'] = date('d/m/Y', strtotime(' + 3 day'));;
            $outcome['Outcome']['data_competencia'] = date('01/m/Y');
            $outcome['Outcome']['created'] = date('d/m/Y H:i:s');
            $outcome['Outcome']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->Outcome->create();
            $this->Outcome->save($outcome);
        }

        $this->Flash->set(__('Pagamento gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);

        echo json_encode(['success' => true]);
    }

    public function boletos($id)
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Order.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $_GET['q'] . "%", 'Supplier.nome_fantasia LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $data = $this->Paginator->paginate('PaymentImportLog', $condition);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Boletos' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'id'));
    }

    public function baixar_boleto_fornecedor($id)
    {
        $this->autoRender = false;

        $log = $this->PaymentImportLog->findById($id);
        $this->redirect('/private_files/baixar_boleto/boletos_operadoras/' . $log['PaymentImportLog']['customer_user_id'] . '/boleto-' . $log['PaymentImportLog']['order_id'] . '-' . $log['PaymentImportLog']['supplier_id'] . '_pdf');
    }

    public function saldos($id)
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Order.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['OrderBalance.document LIKE' => "%" . $_GET['q'] . "%", 'CustomerUser.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['OrderBalance.tipo' => $_GET['t']]);
        }

        if (isset($_GET['exportar'])) {
            $nome = 'movimentacoes_' . date('d_m_Y_H_i_s') . '.xlsx';

            $data = $this->OrderBalance->find('all', [
                'conditions' => $condition,
            ]);

            $this->ExcelGenerator->gerarExcelPedidoMovimentacoes($nome, $data);

            $this->redirect("/files/excel/" . $nome);
        }

        $data = $this->Paginator->paginate('OrderBalance', $condition);

        $order = $this->Order->findById($id);

        $order_balances_total = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $id, "OrderBalance.tipo" => 1], 'fields' => 'SUM(OrderBalance.total) as total']);
        $order_balances_total2 = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $id, "OrderBalance.tipo" => 2], 'fields' => 'SUM(OrderBalance.total) as total']);
        $order_balances_total3 = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $id, "OrderBalance.tipo" => 3], 'fields' => 'SUM(OrderBalance.total) as total']);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Movimentação' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'order', 'order_balances_total', 'order_balances_total2', 'order_balances_total3'));
    }

    public function zerosEsq($campo, $tamanho)
    {
        $campo = substr($campo, 0, $tamanho);

        return str_pad($campo, $tamanho, 0, STR_PAD_LEFT);
    }

    public function nota_debito($id)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $order = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup'],
            'conditions' => ['Order.id' => $id],
        ]);

        $itens = $this->OrderItem->find('all', [
            'fields' => [
                'CustomerUserItinerary.benefit_id',
                'sum(CustomerUserItinerary.quantity) as qtd',
                'round(sum(OrderItem.subtotal),2) as valor',
                'round(sum(OrderItem.total),2) as total',
            ],
            'conditions' => ['OrderItem.order_id' => $id],
            'group' => ['CustomerUserItinerary.benefit_id']
        ]);

        $view = new View($this, false);
        $view->layout = false;

        $link = APP . 'webroot';
        // $link = '';
        $view->set(compact("link", "itens", "order"));
        $html = $view->render('../Elements/nota_debito');

        // echo $html;die();

        $this->HtmltoPdf->convert($html, 'nota.pdf', 'download');
    }

    public function relatorio_beneficio($id)
    {

        ini_set('pcre.backtrack_limit', '20000000');
        $this->layout = 'ajax';
        $this->autoRender = false;
        ini_set('max_execution_time', '-1');

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;

        $order = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup'],
            'conditions' => ['Order.id' => $id],
        ]);

        $paginas = $this->OrderItem->find('all', [
            'fields' => ['CustomerUser.name'],
            'contain' => ['CustomerUser'],
            'conditions' => ['OrderItem.order_id' => $id],
            'group' => ['CustomerUser.id'],
            'order' => ['trim(CustomerUser.name)']
        ]);

        $itens = [];
        foreach ($paginas as $pagina) {
            $itens[$pagina['CustomerUser']['id']] = $this->OrderItem->find('all', [
                'contain' => ['CustomerUser', 'CustomerUserItinerary'],
                'fields' => [
                    'OrderItem.*',
                    'CustomerUser.name as nome',
                    'CustomerUser.cpf as cpf',
                    'CustomerUser.matricula as matricula',
                    'CustomerUserItinerary.benefit_id as matricula',
                    'CustomerUserItinerary.unit_price',
                    'CustomerUserItinerary.benefit_id',
                    'sum(CustomerUserItinerary.quantity) as qtd',
                    'sum(OrderItem.subtotal) as valor',
                    'sum(OrderItem.total) as total',
                    'sum(OrderItem.working_days) as working_days',
                    'OrderItem.saldo',
                    'OrderItem.pedido_operadora'

                ],
                'conditions' => [
                    'OrderItem.order_id' => $id,
                    'CustomerUser.id' => $pagina['CustomerUser']['id'],
                ],
                'group' => ['CustomerUser.id', 'OrderItem.id'],
                'order' => ['trim(CustomerUser.name)']
            ]);
        }
        //debug($order);die;

        $link = APP . 'webroot';
        // $link = '';

        $view->set(compact("link", "order", "itens", "paginas"));

        $html = $view->render('../Elements/relatorio_beneficio');
        $this->HtmltoPdf->convert($html, 'relatorio_beneficio.pdf', 'download');
    }

    public function listagem_entrega($id)
    {
        ini_set('pcre.backtrack_limit', '15000000');
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;
        $order = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup'],
            'conditions' => ['Order.id' => $id],
        ]);

        $itens = $this->OrderItem->find('all', [
            'contain' => ['Order', 'CustomerUser', 'CustomerUserItinerary'],
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => ['Customer.id = Order.customer_id'],
                ],
            ],
            'fields' => [
                'OrderItem.*',
                'Customer.documento',
                'Customer.nome_secundario',
                'CustomerUser.name as nome',
                'CustomerUser.cpf as cpf',
                'CustomerUser.matricula as matricula',
                'CustomerUserItinerary.benefit_id as benefit_id',
                'Order.credit_release_date',
                'Order.id',
                'Order.created',
                'CustomerUserItinerary.benefit_id',
                'CustomerUserItinerary.unit_price',
                'sum(CustomerUserItinerary.quantity) as qtd',
                'sum(OrderItem.subtotal) as valor',
                'sum(OrderItem.total) as total',
                'sum(OrderItem.working_days) as working_days',
                'OrderItem.saldo' // Ensure 'desconto' is included
            ],
            'conditions' => ['OrderItem.order_id' => $id],
            'group' => ['OrderItem.id'],
            'order' => ['trim(CustomerUser.name)']
        ]);
        //debug($itens);die;

        $de = $order['Order']['order_period_from_nao_formatado'];
        $para = $order['Order']['order_period_to_nao_formatado'];

        $link = APP . 'webroot';
        $view->set(compact("link", "order", "itens", "de", "para"));

        $html = $view->render('../Elements/listagem_entrega');
        $this->HtmltoPdf->convert($html, 'listagem_entrega.pdf', 'download');
    }

    public function resumo($id)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;
        $order = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'conditions' => ['Order.id' => $id],
        ]);

        $itens = $this->OrderItem->find('all', [
            'contain' => ['Order', 'CustomerUser', 'CustomerUserItinerary',],
            'fields' => [
                'CustomerUser.name as nome',
                'CustomerUser.cpf as cpf',
                'CustomerUser.matricula as matricula',
                'CustomerUserItinerary.benefit_id as matricula',
                'Order.credit_release_date',
                'CustomerUserItinerary.benefit_id',
                'CustomerUserItinerary.unit_price',
                'sum(CustomerUserItinerary.quantity) as qtd',
                'sum(OrderItem.subtotal) as valor',
                'sum(OrderItem.total) as total',
                'sum(OrderItem.working_days) as working_days',
                'OrderItem.saldo',

            ],
            'conditions' => ['OrderItem.order_id' => $id],
            'group' => ['Order.id']
        ]);

        $suppliersCount = $this->OrderItem->find('count', [
            'conditions' => ['OrderItem.order_id' => $id],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ]
            ],
            'group' => ['Supplier.id'],
            'fields' => ['Supplier.id']
        ]);

        $usersCount = $this->OrderItem->find('count', [
            'conditions' => ['OrderItem.order_id' => $id],
            'group' => ['OrderItem.customer_user_id'],
            'fields' => ['OrderItem.customer_user_id']
        ]);
        $link = APP . 'webroot';

        $view->set(compact("order", "itens", "suppliersCount", "usersCount", "link"));

        $html = $view->render('../Elements/resumo');
        // echo $html;
        $this->HtmltoPdf->convert($html, 'resumo.pdf', 'download');
    }



    public function cobranca($id)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;
        ini_set('pcre.backtrack_limit', '15000000');
        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;
        $order = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup', 'OrderItem.CustomerUserItinerary'],
            'conditions' => ['Order.id' => $id],
        ]);

        $itens = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id],
            'fields' => [
                'Supplier.razao_social',
                'sum(OrderItem.subtotal) as subtotal',
                'sum(OrderItem.total) as total',
                'sum(OrderItem.transfer_fee) as transfer_fee',
                'sum(OrderItem.commission_fee) as commission_fee',
                'OrderItem.saldo',
            ],

            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ],
            ],
            'group' => ['Benefit.supplier_id']
        ]);

        /*
        debug($itens);

        die;*/

        $link = APP . 'webroot';
        $view->set(compact("link", "order", "itens"));

        $html = $view->render('../Elements/cobranca');

        // Em vez de converter para PDF, exibe o HTML
        // echo $html;

        $this->HtmltoPdf->convert($html, 'Cobranca.pdf', 'download');
    }




    public function relatorio_pedidos()
    {
        ini_set('pcre.backtrack_limit', '15000000');
        ini_set('memory_limit', '-1');
        $this->layout = 'ajax';
        $this->autoRender = false;

        $view = new View($this, false);
        $view->layout = false;

        // Inicializando as condições de filtragem
        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) && $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'Order.id' => $_GET['q'],
                'Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%",
                'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%",
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%",
                'Customer.id LIKE' => "%" . $_GET['q'] . "%"
            ]);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['t']]);
        }

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        if ($get_de != '' && $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], [
                'Order.created between ? and ?' => [$de . ' 00:00:00', $ate . ' 23:59:59']
            ]);
        }

        $get_de_pagamento = isset($_GET['de_pagamento']) ? $_GET['de_pagamento'] : '';
        $get_ate_pagamento = isset($_GET['ate_pagamento']) ? $_GET['ate_pagamento'] : '';

        if ($get_de_pagamento != '' && $get_ate_pagamento != '') {
            $de_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_de_pagamento)));
            $ate_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate_pagamento)));

            $condition['and'] = array_merge($condition['and'], [
                'Income.data_pagamento between ? and ?' => [$de_pagamento . ' 00:00:00', $ate_pagamento . ' 23:59:59']
            ]);
        }

        $data = $this->Order->find('all', [
            'contain' => [
                'Status',
                'Customer',
                'CustomerCreator',
                'EconomicGroup',
                'Income.data_pagamento'
            ],
            'conditions' => $condition,
        ]);


        foreach ($data as $k => $pedido) {
            $suppliersCount = $this->OrderItem->find('count', [
                'conditions' => ['OrderItem.order_id' => $pedido['Order']['id']],
                'joins' => [
                    [
                        'table' => 'benefits',
                        'alias' => 'Benefit',
                        'type' => 'INNER',
                        'conditions' => [
                            'Benefit.id = CustomerUserItinerary.benefit_id'
                        ]
                    ],
                    [
                        'table' => 'suppliers',
                        'alias' => 'Supplier',
                        'type' => 'INNER',
                        'conditions' => [
                            'Supplier.id = Benefit.supplier_id'
                        ]
                    ]
                ],
                'group' => ['Supplier.id'],
                'fields' => ['Supplier.id']
            ]);

            $usersCount = $this->OrderItem->find('count', [
                'conditions' => ['OrderItem.order_id' => $pedido['Order']['id']],
                'group' => ['OrderItem.customer_user_id'],
                'fields' => ['OrderItem.customer_user_id']
            ]);

            $data[$k]['Order']['suppliersCount'] = $suppliersCount;
            $data[$k]['Order']['usersCount'] = $usersCount;
        }

        $link = APP . 'webroot';
        $view->set(compact("link", "data", "get_de", "get_ate"));

        $html = $view->render('../Elements/relatorio_pedidos');

        $this->HtmltoPdf->convert($html, 'relatorio_pedidos.pdf', 'download');
    }

    public function relatorio_processamento($id)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;
        $order = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup'],
            'conditions' => ['Order.id' => $id],
        ]);

        $data = $this->OrderItem->getProcessamentoPedido('all', ['OrderItem.order_id' => $id]);

        $this->ExcelGenerator->gerarExcelOrdersprocessamento('ProcessamentoPedidoOperadora', $data);

        $this->redirect('/private_files/baixar/excel/ProcessamentoPedidoOperadora_xlsx');
    }

    public function relatorio_processamento_index()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) && $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'Order.id' => $_GET['q'],
                'Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%",
                'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%",
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%",
                'Customer.id LIKE' => "%" . $_GET['q'] . "%"
            ]);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['t']]);
        }

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        if ($get_de != '' && $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], [
                'Order.created between ? and ?' => [$de . ' 00:00:00', $ate . ' 23:59:59']
            ]);
        }

        $get_de_pagamento = isset($_GET['de_pagamento']) ? $_GET['de_pagamento'] : '';
        $get_ate_pagamento = isset($_GET['ate_pagamento']) ? $_GET['ate_pagamento'] : '';

        if ($get_de_pagamento != '' && $get_ate_pagamento != '') {
            $de_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_de_pagamento)));
            $ate_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate_pagamento)));

            $condition['and'] = array_merge($condition['and'], [
                'Income.data_pagamento between ? and ?' => [$de_pagamento . ' 00:00:00', $ate_pagamento . ' 23:59:59']
            ]);
        }

        $orders = $this->Order->find('all', [
            'contain' => ['Customer', 'EconomicGroup'],
            'conditions' => $condition,
        ]);

        $data = [];
        foreach ($orders as $order) {
            $orderItems = $this->OrderItem->getProcessamentoPedido('all', ['OrderItem.order_id' => $order['Order']['id']]);

            $data = array_merge($data, $orderItems);
        }

        $this->ExcelGenerator->gerarExcelOrdersprocessamento('ProcessamentoPedidoOperadora', $data);

        $this->redirect('/private_files/baixar/excel/ProcessamentoPedidoOperadora_xlsx');
    }

    public function processamentopdf($id)
    {
        ini_set('pcre.backtrack_limit', '35000000');

        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;

        $order = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup'],
            'conditions' => ['Order.id' => $id],
        ]);



        $data = $this->OrderItem->find('all', [
            'fields' => [
                'Order.*',
                'OrderItem.*',
                'Customer.*',
                'Status.*',
                'CustomerUser.*',
                'EconomicGroups.*',
                'Supplier.*',
                'Benefit.*',
                'CustomerUserItinerary.*',

            ],
            'conditions' => ['OrderItem.order_id' => $id],
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
                    'table' => 'economic_groups',
                    'alias' => 'EconomicGroups',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Order.economic_group_id = EconomicGroups.id'
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
                ]
            ],
            'group' => ['OrderItem.id'],
            'order' => ['trim(CustomerUser.name)']
        ]);

        $link = APP . 'webroot';
        $view->set(compact("link", "data", "order"));

        $html = $view->render('../Elements/processamentopdf');

        //echo $html;
        $this->HtmltoPdf->convert($html, 'processamento.pdf', 'download');
    }

    private function getCommissionPerc($benefitType, $proposal)
    {
        $commissionPerc = 0;
        switch ($benefitType) {
            case 1:
            case 2:
            case 3:
            case 9:
                $commissionPerc = $proposal['Proposal']['transport_adm_fee'];
                break;
            case 4:
                $commissionPerc = $proposal['Proposal']['meal_adm_fee'];
                break;
            case 8:
                $commissionPerc = $proposal['Proposal']['fuel_adm_fee'];
                break;
            case 5:
            case 6:
            case 7:
            case 10:
                $commissionPerc = $proposal['Proposal']['multi_card_adm_fee'];
                break;
        }

        return $commissionPerc;
    }

    public function priceFormatBeforeSave($price)
    {
        if (is_numeric($price)) {
            return $price;
        }
        if ($price == '') {
            return 0;
        }
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }

    public function getOrderByCustomer($customerId)
    {
        $this->layout = false;
        $this->autoRender = false;

        $orders = $this->Order->find('all', [
            'contain' => ['Customer'],
            'fields' => ['Order.id', 'concat(Order.id, " - ", Customer.codigo_associado, " - ", Customer.nome_primario) as name'],
            'order' => ['Order.id' => 'asc'],
            'conditions' => ['Order.customer_id' => $customerId]
        ]);

        echo json_encode($orders);
    }

    public function getCustomerGE($customerId)
    {
        $this->layout = false;
        $this->autoRender = false;

        $customers = $this->Customer->find('first', [
            'fields' => ['Customer.flag_gestao_economico'],
            'conditions' => ['Customer.id' => $customerId]
        ]);

        echo json_encode($customers);
    }

    public function baixar_beneficiarios($id)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;

        $nome = 'beneficiarios_pedido_' . $id . '.xlsx';

        $data = $this->CustomerUser->find_pedido_beneficiarios_info($id);

        $this->ExcelGenerator->gerarExcelPedidosBeneficiariosPIX($nome, $data);

        $this->redirect("/files/excel/" . $nome);
    }

    public function compras($id)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Order->id = $id;
        $old_order = $this->Order->read();

        $this->request->data = $this->Order->read();
        $order = $this->Order->findById($id);

        $this->Paginator->settings = ['OrderItem' => [
            'limit' => 200,
            'order' => ['CustomerUser.name' => 'asc'],
            'fields' => ['OrderItem.*', 'CustomerUserItinerary.*', 'Benefit.*', 'Order.*', 'CustomerUser.*', 'Supplier.*'],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ]

            ]
        ]];

        $condition = ["and" => ['Order.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $_GET['q'] . "%", 'CustomerUser.cpf LIKE' => "%" . $_GET['q'] . "%", 'Benefit.name LIKE' => "%" . $_GET['q'] . "%", 'Benefit.code LIKE' => "%" . $_GET['q'] . "%", 'Supplier.nome_fantasia LIKE' => "%" . $_GET['q'] . "%", 'OrderItem.status_processamento LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['sup']) and $_GET['sup'] != '') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Supplier.id' => $_GET['sup']]);
        }

        if (isset($_GET['stp']) and $_GET['stp'] != '') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['OrderItem.status_processamento' => $_GET['stp']]);
        }

        $items = $this->Paginator->paginate('OrderItem', $condition);

        $items_total = $this->OrderItem->find('all', [
            'fields' => ['SUM(OrderItem.subtotal) as subtotal', 'SUM(OrderItem.transfer_fee) as transfer_fee', 'SUM(OrderItem.commission_fee) as commission_fee', 'SUM(OrderItem.total) as total', 'SUM(OrderItem.saldo) as saldo'],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ]

            ],
            'conditions' => $condition,
        ]);

        $action = 'Compras';
        $breadcrumb = ['Cadastros' => '', 'Compras' => '', 'Alterar Compras' => ''];

        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items', 'items_total'));
    }

    public function alter_item_status_processamento()
    {
        $this->autoRender = false;

        $itemOrderId        = isset($this->request->data['orderItemIds']) ? $this->request->data['orderItemIds'] : false;
        $statusProcess      = isset($this->request->data['v_status_processamento']) ? $this->request->data['v_status_processamento'] : false;
        $pedido_operadora   = isset($this->request->data['v_pedido_operadora']) ? $this->request->data['v_pedido_operadora'] : false;
        $data_entrega       = isset($this->request->data['v_data_entrega']) ? $this->request->data['v_data_entrega'] : false;
        $motivo             = isset($this->request->data['v_motivo']) ? $this->request->data['v_motivo'] : false;

        foreach ($itemOrderId as $key => $value) {
            $orderItem = $this->OrderItem->findById($value);

            $dados_log = [
                "old_value" => $orderItem['OrderItem']['status_processamento'] ? $orderItem['OrderItem']['status_processamento'] : ' ',
                "new_value" => $statusProcess,
                "route" => "orders/compras",
                "log_action" => "Alterou",
                "log_table" => "OrderItem",
                "primary_key" => $value,
                "parent_log" => 0,
                "user_type" => "ADMIN",
                "user_id" => CakeSession::read("Auth.User.id"),
                "message" => "O status_processamento do item foi alterado com sucesso",
                "log_date" => date("Y-m-d H:i:s"),
                "data_cancel" => "1901-01-01",
                "usuario_data_cancel" => 0,
                "ip" => $_SERVER["REMOTE_ADDR"]
            ];

            $this->Log->create();
            $this->Log->save($dados_log);

            $data = [
                'OrderItem' => [
                    'id' => $orderItem['OrderItem']['id'],
                    'status_processamento' => $statusProcess,
                    'pedido_operadora' => $pedido_operadora,
                    'data_entrega' => $data_entrega,
                    'updated_user_id' => CakeSession::read("Auth.User.id"),
                    'updated' => date('Y-m-d H:i:s'),
                ]
            ];

            if ($motivo) {
                $data['OrderItem']['motivo_processamento'] = $motivo;
            }

            $this->OrderItem->save($data);

            if ($statusProcess == 'CREDITO_INCONSISTENTE') {
                $orderBalanceData = [
                    'order_id' => $orderItem['Order']['id'],
                    'order_item_id' => $orderItem['OrderItem']['id'],
                    'customer_user_id' => $orderItem['CustomerUser']['id'],
                    'benefit_id' => $orderItem['CustomerUserItinerary']['benefit_id'],
                    'document' => $orderItem['CustomerUser']['cpf'],
                    'total' => $orderItem['OrderItem']['subtotal'],
                    'pedido_operadora' => $pedido_operadora,
                    'observacao' => $motivo,
                    'tipo' => 3,
                    'created' => date('Y-m-d H:i:s'),
                    'user_created_id' => CakeSession::read("Auth.User.id")
                ];

                $this->OrderBalance->create();
                $this->OrderBalance->save($orderBalanceData);
            }
        }

        echo json_encode(['success' => true]);
    }

    public function alter_item_status_processamento_order_all()
    {
        $this->autoRender = false;

        $order_id = $this->request->data['order_id'];

        $statusProcess      = isset($this->request->data['v_status_processamento']) ? $this->request->data['v_status_processamento'] : false;
        $pedido_operadora   = isset($this->request->data['v_pedido_operadora']) ? $this->request->data['v_pedido_operadora'] : false;
        $data_entrega       = isset($this->request->data['v_data_entrega']) ? $this->request->data['v_data_entrega'] : false;
        $motivo             = isset($this->request->data['v_motivo']) ? $this->request->data['v_motivo'] : false;

        $itemOrderId = isset($this->request->data['notOrderItemIds']) ? $this->request->data['notOrderItemIds'] : false;

        $q      = isset($this->request->data['curr_q']) ? $this->request->data['curr_q'] : false;
        $sup    = isset($this->request->data['curr_sup']) ? $this->request->data['curr_sup'] : false;
        $stp    = isset($this->request->data['curr_stp']) ? $this->request->data['curr_stp'] : false;

        $condition = ["and" => ['Order.id' => $order_id, 'OrderItem.id !=' => $itemOrderId], "or" => []];

        if (isset($q) and $q != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $q . "%", 'CustomerUser.cpf LIKE' => "%" . $q . "%", 'Benefit.name LIKE' => "%" . $q . "%", 'Benefit.code LIKE' => "%" . $q . "%", 'Supplier.nome_fantasia LIKE' => "%" . $q . "%", 'OrderItem.status_processamento LIKE' => "%" . $q . "%"]);
        }

        if (isset($sup) and $sup != '') {
            $condition['and'] = array_merge($condition['and'], ['Supplier.id' => $sup]);
        }

        if (isset($stp) and $stp != '') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['OrderItem.status_processamento' => $stp]);
        }

        $items = $this->OrderItem->find('all', [
            'fields' => ['OrderItem.id'],
            'conditions' => $condition,
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id'
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id'
                    ]
                ]

            ]
        ]);

        foreach ($items as $item) {
            $orderItem = $this->OrderItem->findById($item['OrderItem']['id']);

            $dados_log = [
                "old_value" => $orderItem['OrderItem']['status_processamento'] ? $orderItem['OrderItem']['status_processamento'] : ' ',
                "new_value" => $statusProcess,
                "route" => "orders/compras",
                "log_action" => "Alterou",
                "log_table" => "OrderItem",
                "primary_key" => $item['OrderItem']['id'],
                "parent_log" => 0,
                "user_type" => "ADMIN",
                "user_id" => CakeSession::read("Auth.User.id"),
                "message" => "O status_processamento do item foi alterado com sucesso",
                "log_date" => date("Y-m-d H:i:s"),
                "data_cancel" => "1901-01-01",
                "usuario_data_cancel" => 0,
                "ip" => $_SERVER["REMOTE_ADDR"]
            ];

            $this->Log->create();
            $this->Log->save($dados_log);

            $data = [
                'OrderItem' => [
                    'id' => $orderItem['OrderItem']['id'],
                    'status_processamento' => $statusProcess,
                    'pedido_operadora' => $pedido_operadora,
                    'data_entrega' => $data_entrega,
                    'updated_user_id' => CakeSession::read("Auth.User.id"),
                    'updated' => date('Y-m-d H:i:s'),
                ]
            ];

            if ($motivo) {
                $data['OrderItem']['motivo_processamento'] = $motivo;
            }

            $this->OrderItem->save($data);

            if ($statusProcess == 'CREDITO_INCONSISTENTE') {
                $orderBalanceData = [
                    'order_id' => $orderItem['Order']['id'],
                    'order_item_id' => $orderItem['OrderItem']['id'],
                    'customer_user_id' => $orderItem['CustomerUser']['id'],
                    'benefit_id' => $orderItem['CustomerUserItinerary']['benefit_id'],
                    'document' => $orderItem['CustomerUser']['cpf'],
                    'total' => $orderItem['OrderItem']['subtotal'],
                    'pedido_operadora' => $pedido_operadora,
                    'observacao' => $motivo,
                    'tipo' => 3,
                    'created' => date('Y-m-d H:i:s'),
                    'user_created_id' => CakeSession::read("Auth.User.id")
                ];

                $this->OrderBalance->create();
                $this->OrderBalance->save($orderBalanceData);
            }
        }

        echo json_encode(['success' => true]);
    }

    /*
    * Novo upload the beneficiarios
    */
    private function parseNewCsv($customerId, $tmpFile)
    {
        // COLUMNS:
        // CNPJ CLIENTE;NOME;CPF;RG;DATA NASCIMENTO;NOME DA MÃE;DIAS UTEIS;
        // CODIGO OPERADORA;CODIGO BENEFICIO;NUMERO CARTAO;VALOR_UNIT;QUANTIDADE;
        // FAIXA SALARIAL;TIPO CHAVE (CNPJ-CPF-E_MAIL-CELULAR-ALEATORIA);CHAVE PIX;
        // MATRICULA;CODIGO BANCO;AGENCIA;CONTA;DIGITO DA CONTA;CENTRO DE CUSTO;DEPARTAMENTO;GRUPO ECONOMICO

        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $has_valor_unitario_invalido = false;
        $line = 0;
        $invalidCpfLines = [];
        $missingMatriculaLines = [];
        $duplicateMatriculaLines = [];
        $invalidUnitPriceLines = [];

        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                $line++;
                continue;
            }

            if (count($row) <= 5) {
                return ['success' => false, 'error' => 'Arquivo inválido.'];
            }

            $cpfToValidate = trim($row[2]);

            // if (empty($cpfToValidate) || !$this->isValidCPF($cpfToValidate)) {
            //     $invalidCpfLines[] = $line + 1;
            // }

            $codigoOperadora = trim($row[7]);
            $codigoBeneficio = trim($row[8]);
            $dataNascimento = $row[4];
            $matricula = $row[15];

            // if (!empty($matricula)) {
            //     $matriculaExists = $this->CustomerUser->find('first', [
            //         'conditions' => [
            //             'CustomerUser.matricula' => $matricula,
            //             'CustomerUser.customer_id' => $customerId,
            //             'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
            //             'NOT' => [
            //                 "REPLACE(REPLACE(CustomerUser.cpf, '-', ''), '.', '')" => preg_replace('/\D/', '', $row[2])
            //             ]
            //         ]
            //     ]);
            //     if (!empty($matriculaExists)) {
            //         $duplicateMatriculaLines[] = $line + 1;
            //     }
            // }

            $benefit = $this->Benefit->find('first', [
                'conditions' => [
                    'Benefit.supplier_id' => $codigoOperadora,
                    'Benefit.code' => $codigoBeneficio,
                    'Benefit.data_cancel' => '1901-01-01 00:00:00' // Only active benefits
                ],
                'fields' => ['Benefit.id', 'Benefit.is_variable']
            ]);

            if (empty($benefit) || empty($dataNascimento)) {
                $line++;
                continue; // Skip if no benefit is found
            }

            $unitPrice = $row[10];
            $is_variable = (int)$benefit['Benefit']['is_variable'] === 1;

            $unitPriceRaw = $unitPrice;
            $unitPrice = str_replace(".", "", $unitPrice);
            $unitPrice = (float)str_replace(",", ".", $unitPrice);

            if (($is_variable && (empty($unitPriceRaw) || $unitPrice <= 0))) {
                $invalidUnitPriceLines[] = $line + 1;
            }

            $line++;
        }

        // After the foreach, return a summary if any errors were found
        $errorSummary = [];
        if (!empty($invalidCpfLines)) {
            $errorSummary[] = 'CPF inválido nas linhas: ' . implode(', ', $invalidCpfLines);
        }
        if (!empty($missingMatriculaLines)) {
            $errorSummary[] = 'Matrícula não informada nas linhas: ' . implode(', ', $missingMatriculaLines);
        }
        if (!empty($duplicateMatriculaLines)) {
            $errorSummary[] = 'Matrícula já cadastrada para este cliente nas linhas: ' . implode(', ', $duplicateMatriculaLines);
        }
        if (!empty($invalidUnitPriceLines)) {
            $errorSummary[] = 'Favor verificar os valores unitários do arquivo nas linhas: ' . implode(', ', $invalidUnitPriceLines);
        }

        if (!empty($errorSummary)) {
            return ['success' => false, 'error' => implode('<br>', $errorSummary)];
        }

        if ($has_valor_unitario_invalido) {
            return ['success' => false, 'error' => 'Favor verificar os valores unitários do arquivo.'];
        }

        $line = 0;
        $unitPriceMapping = [];
        $customerUsersIds = [];

        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                $line++;
                continue;
            }

            // Map all CSV fields to variables
            $cnpjCliente = $row[0];                           // CNPJ CLIENTE
            $name = $row[1];                                  // NOME
            $cpf = $this->ensureLeadingZeroes(trim($row[2])); // CPF
            $rg = trim($row[3]);                                    // RG
            $dataNascimento = $row[4];                        // DATA NASCIMENTO
            $nomeMae = $row[5];                               // NOME DA MÃE
            $workingDays = (int)$row[6];                      // DIAS UTEIS
            $codigoOperadora = trim($row[7]);                 // CODIGO OPERADORA
            $codigoBeneficio = trim($row[8]);                 // CODIGO BENEFICIO
            $numeroCartao = $row[9];                          // NUMERO CARTAO
            $unitPrice = $row[10];                            // VALOR_UNIT
            $quantity = (int)$row[11];                        // QUANTIDADE
            $faixaSalarial = $row[12];                        // FAIXA SALARIAL
            $tipoChavePix = $row[13];                         // TIPO CHAVE PIX
            $chavePix = isset($row[14]) ? $row[14] : '';      // CHAVE PIX
            $matricula = isset($row[15]) ? $row[15] : '';     // MATRICULA
            $codigoBanco = isset($row[16]) ? $row[16] : '';   // CODIGO BANCO
            $agencia = isset($row[17]) ? $row[17] : '';       // AGENCIA
            $conta = isset($row[18]) ? $row[18] : '';         // CONTA
            $digitoConta = isset($row[19]) ? $row[19] : '';   // DIGITO DA CONTA
            $centroCusto = isset($row[20]) ? $row[20] : '';   // CENTRO DE CUSTO
            $departamento = isset($row[21]) ? $row[21] : ''; // DEPARTAMENTO
            $grupoEconomico = isset($row[22]) ? $row[22] : ''; // GRUPO ECONOMICO

            // Find the benefit ID using the supplier_id (codigoOperadora) and code (codigoBeneficio)
            $benefit = $this->Benefit->find('first', [
                'conditions' => [
                    'Benefit.supplier_id' => $codigoOperadora,
                    'Benefit.code' => $codigoBeneficio,
                    'Benefit.status_id' => 1,
                    'Supplier.status_id' => 1,
                    'Benefit.data_cancel' => '1901-01-01 00:00:00'
                ],
                'fields' => ['Benefit.id', 'Benefit.is_variable', 'Benefit.unit_price']
            ]);

            if (empty($benefit) || empty($dataNascimento)) {
                $line++;
                continue;
            }

            // Pega o ID baseado nos textos de centro de custo, departamento e grupo econômico da planilha
            $extra_ids = $this->getExtraIds($customerId, $centroCusto, $departamento, $grupoEconomico);

            $benefitId = $benefit['Benefit']['id'];
            $is_variable = (int)$benefit['Benefit']['is_variable'] === 1;

            // save to
            // centroCusto = CustomerUser.customer_cost_center_id
            // departamento = CustomerUser.customer_departments_id
            // grupoEconomico = EconomicGroup.EconomicGroup (customer_user_id, economic_group_id)

            if(empty($numeroCartao)){
                // Bypass beforeFind by using query=false
                $lastItinerary = $this->CustomerUserItinerary->find('first', [
                    'conditions' => [
                        "REPLACE(REPLACE(CustomerUser.cpf, '-', ''), '.', '')" => $cpf,
                        'CustomerUserItinerary.card_number !=' => '',
                        'CustomerUserItinerary.card_number IS NOT NULL',
                        'CustomerUserItinerary.customer_id' => $customerId,
                        'CustomerUserItinerary.benefit_id' => $benefitId
                    ],
                    'joins' => [
                        [
                            'table' => 'customer_users',
                            'alias' => 'CustomerUser',
                            'type' => 'INNER',
                            'conditions' => [
                                'CustomerUser.id = CustomerUserItinerary.customer_user_id',
                            ]
                        ]
                    ],
                    'order' => ['CustomerUserItinerary.created' => 'DESC'],
                    'limit' => 1,
                    'recursive' => -1,
                    'callbacks' => false // desabilita beforeFind para buscar todo tipo de cartao
                ]);
                if (!empty($lastItinerary) && !empty($lastItinerary['CustomerUserItinerary']['card_number'])) {
                    $numeroCartao = $lastItinerary['CustomerUserItinerary']['card_number'];
                }
            }

            // Find or create the CustomerUser
            $existingUser = $this->CustomerUser->find('first', [
                'conditions' => [
                    "REPLACE(REPLACE(CustomerUser.cpf, '-', ''), '.', '')" => $cpf,
                    'CustomerUser.customer_id' => $customerId,
                    'CustomerUser.data_cancel' => '1901-01-01 00:00:00' // Only active users
                ]
            ]);

            if (empty($existingUser)) {
                // Create a new CustomerUser record
                $this->CustomerUser->create();
                $this->CustomerUser->save([
                    'CustomerUser' => [
                        'cpf' => $cpf,
                        'customer_id' => $customerId,
                        'name' => $name,
                        'rg' => $rg,
                        'data_nascimento' => $dataNascimento,
                        'nome_mae' => $nomeMae,
                        'status_id' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'data_cancel' => '1901-01-01 00:00:00',
                        'matricula' => $matricula,
                        'customer_cost_center_id' => $extra_ids['cost_center_id'],
                        'customer_departments_id' => $extra_ids['customer_department_id'],
                    ],
                    'EconomicGroup' => [
                        'EconomicGroup' => $extra_ids['economic_group_id']
                    ]
                ]);
                $customerUserId = $this->CustomerUser->id;
            } else {
                $customerUserId = $existingUser['CustomerUser']['id'];
            }

            // Add the user ID to the list, ensuring uniqueness
            if (!in_array($customerUserId, $customerUsersIds)) {
                $customerUsersIds[] = $customerUserId;
            }

            // Check for a CustomerUserAddress
            $existingAddress = $this->CustomerUserAddress->find('first', [
                'conditions' => [
                    'CustomerUserAddress.customer_user_id' => $customerUserId,
                    'CustomerUserAddress.data_cancel' => '1901-01-01 00:00:00' // Only active addresses
                ]
            ]);

            if (empty($existingAddress)) {
                // Get the Customer's address information
                $customer = $this->Customer->findById($customerId);

                if (!empty($customer)) {
                    $this->CustomerUserAddress->create();
                    $this->CustomerUserAddress->save([
                        'CustomerUserAddress' => [
                            'customer_id' => $customerId,
                            'customer_user_id' => $customerUserId,
                            'address_line' => $customer['Customer']['endereco'],
                            'address_number' => isset($customer['Customer']['numero']) ? $customer['Customer']['numero'] : null,
                            'address_complement' =>  isset($customer['Customer']['complemento']) ? $customer['Customer']['complemento'] : null,
                            'neighborhood' => $customer['Customer']['bairro'],
                            'city' => $customer['Customer']['cidade'],
                            'state' => $customer['Customer']['estado'],
                            'zip_code' => preg_replace('/\D/', '', $customer['Customer']['cep']),
                            'data_cancel' => '1901-01-01 00:00:00', // Active status
                        ]
                    ]);
                }
            }

            // Check for an existing itinerary with the same customer_user_id and benefit_id
            $existingItinerary = $this->CustomerUserItinerary->find('first', [
                'conditions' => [
                    'CustomerUserItinerary.customer_user_id' => $customerUserId,
                    'CustomerUserItinerary.benefit_id' => $benefitId,
                    'CustomerUserItinerary.status_id' => 1,
                    'CustomerUserItinerary.customer_id' => $customerId,
                    'CustomerUserItinerary.data_cancel' => '1901-01-01 00:00:00' // Only active itineraries
                ]
            ]);

            if (!empty($existingItinerary) && $is_variable) {
                // Set the existing itinerary as excluded
                $this->CustomerUserItinerary->id = $existingItinerary['CustomerUserItinerary']['id'];
                $this->CustomerUserItinerary->saveField('data_cancel', date('Y-m-d H:i:s'));
            }

            $unitPriceForm = $this->priceFormatBeforeSave($unitPrice);

            $idItinerary = 0;
            if (empty($existingItinerary) || $is_variable) {

                if (empty($existingItinerary) && !$is_variable) {
                    $unitPriceForm = $benefit['Benefit']['unit_price_not_formated'];
                    $unitPrice = $benefit['Benefit']['unit_price'];
                }

                $this->CustomerUserItinerary->create();
                $this->CustomerUserItinerary->save([
                    'CustomerUserItinerary' => [
                        'customer_user_id' => $customerUserId,
                        'benefit_id' => $benefitId,
                        'customer_id' => $customerId,
                        'working_days' => $workingDays,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'price_per_day_non' => ($unitPriceForm * $quantity), // Avoid division by zero
                        'card_number' => $numeroCartao,
                        'data_cancel' => '1901-01-01 00:00:00',
                        'status_id' => 1
                    ]
                ]);

                $idItinerary = $this->CustomerUserItinerary->id;
            } else {
                $idItinerary = $existingItinerary['CustomerUserItinerary']['id'];

                $this->CustomerUserItinerary->id = $idItinerary;
                $this->CustomerUserItinerary->save([
                    'CustomerUserItinerary' => [
                        'card_number' => $numeroCartao
                    ]
                ]);
            }

            // Check if at least one pair is provided
            $hasBankPair = !empty($agencia) && !empty($conta);
            $hasPixPair  = !empty($tipoChavePix) && !empty($chavePix);

            if ($hasBankPair || $hasPixPair) {
                // Prefer to search by conta if available, otherwise by chavePix
                $searchConditions = [
                    'CustomerUserBankAccount.customer_user_id' => $customerUserId,
                    'CustomerUserBankAccount.data_cancel' => '1901-01-01 00:00:00' // Only active accounts
                ];

                if ($hasBankPair) {
                    $searchConditions['CustomerUserBankAccount.acc_number'] = $conta;
                } elseif ($hasPixPair) {
                    $searchConditions['CustomerUserBankAccount.pix_id'] = $chavePix;
                }

                $existingBankAccount = $this->CustomerUserBankAccount->find('first', [
                    'conditions' => $searchConditions
                ]);

                if (empty($existingBankAccount)) {
                    if (empty($codigoBanco)) {
                        $codigoBanco = 11;
                    }

                    $data = [
                        'customer_id'       => $customerId,
                        'customer_user_id'  => $customerUserId,
                        'account_type_id'   => 1,
                        'bank_code_id'      => $codigoBanco,
                        'data_cancel'       => '1901-01-01 00:00:00', // Active status
                        'branch_number'     => $agencia,
                        'acc_number'        => $conta,
                        'acc_digit'         => $digitoConta,
                        'pix_type'          => $tipoChavePix,
                        'pix_id'            => $chavePix
                    ];

                    $this->CustomerUserBankAccount->create();
                    $this->CustomerUserBankAccount->save(['CustomerUserBankAccount' => $data]);
                }
            }

            // Map the unit price data for the user
            $unitPriceMapping[$customerUserId][] = [
                'unitPrice' => $unitPriceForm,
                'unitPriceNonForm' => $unitPrice,
                'workingDays' => $workingDays,
                'quantity' => $quantity,
                'benefitId' => $benefitId,
                'newCsv' => true,
                'idItinerary' => $idItinerary
            ];

            $line++;
        }

        return ['customerUsersIds' => $customerUsersIds, 'unitPriceMapping' => $unitPriceMapping];
    }

    public function upload_saldo_csv_all()
    {
        $ret = $this->parseCSVSaldoAll($this->request->data['file']['tmp_name']);

        $groupTpOrder = [];
        $groupOrder = [];

        foreach ($ret['data'] as $item) {
            $keyTp = $item['tipo'] . '-' . $item['order_id'];
            $keyOr = $item['order_id'];

            if (!isset($groupTpOrder[$keyTp])) {
                $groupTpOrder[$keyTp] = [
                    'tipo' => $item['tipo'],
                    'order_id' => $item['order_id'],
                    'order_item_ids' => []
                ];
            }

            if (!isset($groupOrder[$keyOr])) {
                $groupOrder[$keyOr] = ['order_id' => $item['order_id']];
            }

            $groupTpOrder[$keyTp]['order_item_ids'][] = $item['order_item_id'];
        }

        foreach ($groupTpOrder as $item) {
            if ($item['order_id']) {
                foreach ($item['order_item_ids'] as $itemId) {
                    $this->OrderBalance->update_cancel_balances($item['order_id'], $item['tipo'], CakeSession::read("Auth.User.id"), $itemId);
                }
            }
        }

        foreach ($ret['data'] as $data) {
            if ($data['tipo']) {
                $benefit = $this->Benefit->find('first', ['conditions' => ['Benefit.code' => $data['benefit_code']]]);

                if (isset($benefit['Benefit'])) {
                    $benefit_id = $benefit['Benefit']['id'];
                } else {
                    $benefit_id = null;
                }

                $orderBalanceData = [
                    'order_id' => $data['order_id'],
                    'order_item_id' => $data['order_item_id'],
                    'customer_user_id' => $data['customer_user_id'],
                    'benefit_id' => $benefit_id,
                    'document' => $data['document'],
                    'total' => $data['total'],
                    'pedido_operadora' => $data['pedido_operadora'],
                    'tipo' => $data['tipo'],
                    'observacao' => $data['observacao'],
                    'created' => date('Y-m-d H:i:s'),
                    'user_created_id' => CakeSession::read("Auth.User.id")
                ];

                $this->OrderBalance->create();
                $this->OrderBalance->save($orderBalanceData);
            }
        }

        foreach ($groupOrder as $item) {
            if ($item['order_id']) {
                $this->OrderBalance->update_order_item_saldo($item['order_id'], CakeSession::read("Auth.User.id"));
            }
        }

        $file = new File($this->request->data['file']['name']);
        $dir = new Folder(APP . "webroot/files/order_balances_all/", true);

        $file = $this->Uploader->up($this->request->data['file'], $dir->path);

        $orderBalanceFile = [
            'file_name' => $file['nome'],
            'user_creator_id' => CakeSession::read("Auth.User.id"),
            'created' => date('Y-m-d H:i:s'),
        ];

        $this->OrderBalanceFile->create();
        $this->OrderBalanceFile->save($orderBalanceFile);

        $this->Flash->set(__('Movimentações incluídas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect('/reports/importar_movimentacao');
    }

    private function ensureLeadingZeroes($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);


        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        return $cpf;
    }

    private function parseCSVSaldoAll($tmpFile)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $rec = iterator_to_array($csv->getRecords());

        $header = array_shift($rec);

        usort($rec, function ($a, $b) {
            return strcmp($a[7], $b[7]);
        });

        array_unshift($rec, $header);

        $line = 0;
        $data = [];

        foreach ($rec as $row) {
            $saldo = 0;

            if ($line == 0 || empty($row[0])) {
                if ($line == 0) {
                    $line++;
                }
                continue;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);

            $existingUser = $this->OrderBalance->find_user_order_items($row[7], $cpf);

            $customer_user_id = null;
            if (isset($existingUser[0]['u'])) {
                $customer_user_id = $existingUser[0]['u']['id'];
            }

            $total = str_replace("R$", "", $row[2]);
            $total = str_replace(" ", "", $total);

            $data[] = [
                'customer_user_id' => $customer_user_id,
                'document' => $row[0],
                'benefit_code' => $row[1],
                'total' => $total,
                'pedido_operadora' => $row[3],
                'order_item_id' => $row[4],
                'tipo' => $row[5],
                'observacao' => $row[6],
                'order_id' => $row[7],
            ];

            $line++;
        }

        return ['data' => $data];
    }

    public function aplicar_desconto()
    {
        $this->autoRender = false;

        $order_id = $this->request->data['order_id'];
        $total_desconto = $this->request->data['total_desconto'];
        $orders_select = $this->request->data['orders_select'];

        $this->OrderDiscount->updateAll(
            [
                'OrderDiscount.data_cancel' => 'CURRENT_DATE',
                'OrderDiscount.usuario_id_cancel' => CakeSession::read("Auth.User.id")
            ],
            [
                'OrderDiscount.order_id' => $order_id
            ]
        );

        foreach ($orders_select as $order_select) {
            $data = [
                'order_id' => $order_id,
                'order_parent_id' => $order_select['order_parent'],
                'created' => date('Y-m-d H:i:s'),
                'user_creator_id' => CakeSession::read("Auth.User.id"),
            ];

            $this->OrderDiscount->create();
            $this->OrderDiscount->save($data);
        }

        $this->Order->save([
            'Order' => [
                'id' => $order_id,
                'desconto' => $total_desconto,
                'user_updated_id' => CakeSession::read("Auth.User.id"),
                'updated' => date('Y-m-d H:i:s'),
            ]
        ]);

        echo json_encode(['success' => true]);
    }

    public function descontos($id)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Order->id = $id;
        $old_order = $this->Order->read();

        $this->request->data = $this->Order->read();
        $order = $this->Order->findById($id);

        $this->Paginator->settings = ['OrderDiscount' => [
            'limit' => 200,
            'order' => ['Order.id' => 'desc'],
            'fields' => [
                'OrderParent.*',
                'Customer.nome_primario'
            ],
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Customer.id = OrderParent.customer_id'
                    ]
                ],
            ],
        ]];

        $condition = ["and" => ['Order.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'OrderParent.id' => $_GET['q'],
                'Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%",
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%",
                'Customer.id LIKE' => "%" . $_GET['q'] . "%"
            ]);
        }

        $orders = $this->Paginator->paginate('OrderDiscount', $condition);

        $action = 'Descontos';
        $breadcrumb = ['Cadastros' => '', 'Descontos' => '', 'Alterar Descontos' => ''];

        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'orders'));
    }

    public function isValidCPF($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    // find extra ids for CostCenter, CustomerDepartment, EconomicGroup
    // return array with ids
    // if not found, create for costCenter and department, not for economicGroup
    private function getExtraIds($customerId, $centroCusto, $departamento, $grupoEconomico)
    {
        $extra_ids = [];

        if (!$this->loadModel('CostCenter')) {
            $this->loadModel('CostCenter');
        }
        if (!$this->loadModel('CustomerDepartment')) {
            $this->loadModel('CustomerDepartment');
        }
        if (!$this->loadModel('EconomicGroup')) {
            $this->loadModel('EconomicGroup');
        }

        if ($centroCusto) {
            $costCenter = $this->CostCenter->find('first', [
                'conditions' => [
                    'CostCenter.name' => $centroCusto,
                    'CostCenter.customer_id' => $customerId,
                    'CostCenter.data_cancel' => '1901-01-01 00:00:00'
                ]
            ]);

            if (empty($costCenter)) {
                $this->CostCenter->create();
                $this->CostCenter->save(['name' => $centroCusto, 'customer_id' => $customerId, 'data_cancel' => '1901-01-01 00:00:00']);
                $extra_ids['cost_center_id'] = $this->CostCenter->id;
            } else {
                $extra_ids['cost_center_id'] = $costCenter['CostCenter']['id'];
            }
        }

        if ($departamento) {
            $customerDepartment = $this->CustomerDepartment->find('first', [
                'conditions' => [
                    'CustomerDepartment.name' => $departamento,
                    'CustomerDepartment.customer_id' => $customerId,
                    'CustomerDepartment.data_cancel' => '1901-01-01 00:00:00'
                ]
            ]);

            if (empty($customerDepartment)) {
                $this->CustomerDepartment->create();
                $this->CustomerDepartment->save([
                    'name' => $departamento,
                    'customer_id' => $customerId,
                    'data_cancel' => '1901-01-01 00:00:00'
                ]);
                $extra_ids['customer_department_id'] = $this->CustomerDepartment->id;
            } else {
                $extra_ids['customer_department_id'] = $customerDepartment['CustomerDepartment']['id'];
            }
        }

        if ($grupoEconomico) {
            $economicGroup = $this->EconomicGroup->find('first', [
                'conditions' => [
                    "REPLACE(REPLACE(REPLACE(EconomicGroup.document, '.', ''), '-', ''), '/', '')" => preg_replace('/\D/', '', $grupoEconomico),
                    'EconomicGroup.customer_id' => $customerId,
                    'EconomicGroup.data_cancel' => '1901-01-01 00:00:00'
                ]
            ]);

            $extra_ids['economic_group_id'] = null;
            if (!empty($economicGroup)) {
                $extra_ids['economic_group_id'] = $economicGroup['EconomicGroup']['id'];
            }
        }

        return $extra_ids;
    }

    public function tempRecalculateFirstOrder($orderId = null, $customerUserId = null)
    {
        if (empty($orderId) || empty($customerUserId)) {
            echo "Missing parameters. Usage: orderId and customerUserId are required.\n";
            return;
        }

        // Find all order items for this specific order and customer user
        $orderItems = $this->OrderItem->find('all', [
            'conditions' => [
                'OrderItem.order_id' => $orderId,
                'OrderItem.customer_user_id' => $customerUserId
            ],
            'recursive' => -1
        ]);

        if (empty($orderItems)) {
            echo "No order items found for Order ID: {$orderId} and Customer User ID: {$customerUserId}\n";
            return;
        }

        echo "Found " . count($orderItems) . " order items to process.\n\n";

        foreach ($orderItems as $orderItem) {
            $orderItemId = $orderItem['OrderItem']['id'];
            
            // Set the data for the OrderItem model
            $this->OrderItem->data = $orderItem;
            
            // Calculate first order
            $firstOrderValue = $this->OrderItem->calculateFirstOrder();
            
            // Update the order item with the new first_order value
            $this->OrderItem->id = $orderItemId;
            $updateResult = $this->OrderItem->saveField('first_order', $firstOrderValue);
            
            if ($updateResult) {
                echo "Order Item ID: {$orderItemId} - First Order: {$firstOrderValue} - Updated successfully\n";
            } else {
                echo "Order Item ID: {$orderItemId} - First Order: {$firstOrderValue} - Failed to update\n";
            }
        }

        echo "\nProcessing completed.\n";
    }
}
