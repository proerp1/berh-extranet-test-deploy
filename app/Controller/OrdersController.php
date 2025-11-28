<?php
App::uses('ApiItau', 'Lib');
App::uses('ApiBtgPactual', 'Lib');
App::uses('RepaymentCalculator', 'Lib');

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
        'EconomicGroupProposal',
        'OrderBalance',
        'Log',
        'Supplier',
        'CustomerUserAddress',
        'BancoPadrao',
        'CustomerUserBankAccount',
        'OrderBalanceFile',
        'BankAccount',
        'OrderDiscount',
        'LogOrderItemsProcessamento',
        'SupplierVolumeTier',
        'OutcomeOrder',
        'OrderDiscountBatch',
        'OrderDiscountBatchItem',
        'Docoutcome'
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
                'Order.id' => "" . $_GET['q'] . "",
                'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%",
                'Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%",
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%",
                'Customer.id LIKE' => "%" . $_GET['q'] . "%"
            ]);
            $filtersFilled = true;
        }

        if (!empty($_GET['cliente'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.customer_id' => $_GET['cliente']]);
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

        if (!empty($_GET['cond_pag'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.condicao_pagamento' => $_GET['cond_pag']]);
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

        $uniqueCustomers = array_unique(Hash::extract($data, '{n}.Order.customer_id'));
        $isSingleCustomer = count($uniqueCustomers) === 1 && count($data) > 0;

        $customers = $this->Customer->find('list', [
            'conditions' => ['Customer.status_id' => 3],
            'fields' => ['id', 'nome_primario'],
            'order' => ['nome_primario' => 'asc']
        ]);

        $conditionsJson = base64_encode(json_encode($condition));

        $benefit_types = [-1 => 'Transporte', 4 => 'PAT', 999 => 'Outros'];

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => ''];

        $this->set(compact('data', 'limit', 'status', 'action', 'breadcrumb', 'customers', 'benefit_types'));
        $this->set(compact('conditionsJson', 'filtersFilled', 'queryString', 'isSingleCustomer'));        
    }

    public function getTotalOrders()
    {
        $this->autoRender = false;
        $this->response->type('json');

        ini_set('memory_limit', '-1');
        
        if (!$this->request->is('ajax')) {
            throw new NotFoundException();
        }
        
        $condition = json_decode(base64_decode($this->request->data('conditions')), true);

        $totalOrders = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'fields' => [
                'sum(Order.subtotal) as subtotal',
                'sum(Order.transfer_fee) as transfer_fee',
                'sum(Order.tpp_fee) as total_tpp',
                'sum(Order.commission_fee) as commission_fee',
                'sum(Order.desconto) as desconto',
                'sum(Order.total) as total',
            ],
            'conditions' => $condition,
            'recursive' => -1
        ]);

        echo json_encode([
            'success' => true,
            'totals' => $totalOrders[0],
            'has_economia' => false
        ]);
    }

    public function getTotalEconomia()
    {
        $this->autoRender = false;
        $this->response->type('json');

        ini_set('memory_limit', '-1');
        
        if (!$this->request->is('ajax')) {
            throw new NotFoundException();
        }
        
        $condition = json_decode(base64_decode($this->request->data('conditions')), true);

        $order_ids = $this->Order->find('list', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'fields' => ['Order.id'],
            'conditions' => $condition,
            'recursive' => -1
        ]);

        $total_economia = 0;
        foreach ($order_ids as $order_id) {
            $extrato = $this->Order->getExtrato($order_id);
            $total_economia += $extrato['v_total_economia'];
        }

        echo json_encode([
            'success' => true,
            'economia' => $total_economia
        ]);
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
            $due_date = $this->request->data['due_date'];

            $condicao_pagamento = isset($this->request->data['condicao_pagamento']) ? $this->request->data['condicao_pagamento'] : 1;
            $prazo = isset($this->request->data['prazo']) ? $this->request->data['prazo'] : null;

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

            $proposal = $this->Order->getProposalForOrder($customerId, $grupo_especifico);

            if (empty($proposal)) {
                $this->Flash->set(__('Cliente não possui uma proposta ativa.'), ['params' => ['class' => "alert alert-danger"]]);
                $this->redirect(['action' => 'index']);
            }

            $customer = $this->Customer->find('first', ['fields' => ['Customer.observacao_notafiscal', 'Customer.flag_gestao_economico', 'Customer.porcentagem_margem_seguranca', 'Customer.qtde_minina_diaria', 'Customer.tipo_ge'], 'conditions' => ['Customer.id' => $customerId], 'recursive' => -1]);

            if ($is_partial == 3 || $is_partial == 4) {
                $pedido_complementar = 2;
            }

            if ($condicao_pagamento != 2) {
                $prazo = null;
            }

            if ($is_consolidated == 2) {
                $b_type_consolidated = $benefit_type_persist == 0 ? '' : $benefit_type_persist;
                $orderId = $this->processConsolidated($customerId, $workingDays, $period_from, $period_to, $is_partial, $credit_release_date, $working_days_type, $grupo_especifico, $b_type_consolidated, $proposal, $pedido_complementar, $condicao_pagamento, $prazo, $due_date);
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
            
            $order_status_id = 83;

            $orderData = [
                'customer_id' => $customerId,
                'customer_address_id' => $customerAddressId,
                'gera_nfse' => $geraNfse,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period_from' => $period_from,
                'order_period_to' => $period_to,
                'status_id' => $order_status_id,
                'is_partial' => $is_partial,
                'pedido_complementar' => $pedido_complementar,
                'credit_release_date' => $credit_release_date,
                'created' => date('Y-m-d H:i:s'),
                'working_days_type' => $working_days_type,
                'benefit_type' => $benefit_type_persist,
                'due_date' => $due_date,
                'nfse_observation' => $obs_notafiscal,
                'flag_gestao_economico' => $customer['Customer']['flag_gestao_economico'],
                'porcentagem_margem_seguranca' => $customer['Customer']['porcentagem_margem_seguranca'],
                'qtde_minina_diaria' => $customer['Customer']['qtde_minina_diaria'],
                'tipo_ge' => $customer['Customer']['tipo_ge'],
                'primeiro_pedido' => ($customer_orders > 1 ? "N" : "S"),
                'condicao_pagamento' => $condicao_pagamento,
                'prazo' => $prazo,
            ];

            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();

                if ($is_partial == 2) {
                    $this->processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type, $proposal);
                }

                $this->Order->id = $orderId;
                $this->recalculateOrderTransferFees($orderId);
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
        $this->recalculateOrderTransferFees($newId);
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
        
        // Após criar todos os itens, recalcular volume tier fees
        $this->recalculateOrderTransferFees($orderId);
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
        
        // Para tipos 1 e 2 (valor fixo e percentual), calcular normalmente
        // Para tipo 3 (volume tier), será calculado posteriormente no nível do pedido
        $transferFee = 0;
        $calculationLog = null;
        
        // Todos os fornecedores agora usam volume tiers - usar RepaymentCalculator
        App::uses('RepaymentCalculator', 'Lib');
        try {
            $quantity = $workingDaysUser;
            $calculationResult = RepaymentCalculator::calculateRepayment(
                $benefit['Supplier']['id'], 
                $quantity, 
                $subtotal
            );
            $transferFee = $calculationResult['repayment_value'];
            
            // Prepare calculation log with new unified system
            $calculationLog = json_encode([
                'type' => $calculationResult['calculation_method'],
                'tier_info' => isset($calculationResult['tier_used']) ? $calculationResult['tier_used'] : null,
                'quantity' => $quantity,
                'base_value' => $subtotal
            ]);
            
        } catch (Exception $e) {
            // Fallback em caso de erro
            $transferFee = 0;
            $calculationLog = json_encode(['error' => $e->getMessage()]);
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
            'calculation_details_log' => $calculationLog,
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

        if(isset($_GET['internal_recalc_proerp'])){
            $this->recalculateOrderTransferFees($id);
            $this->Order->reProcessAmounts($id);
            $this->Order->reprocessFirstOrder($id);
        }
        
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

            if (isset($this->request->data['Order']['credit_release_date'])) {
                $credit_release_date = trim($this->request->data['Order']['credit_release_date']);

                 if (!empty($credit_release_date) && $credit_release_date !== $old_order['Order']['credit_release_date']) {
                    $order['Order']['credit_release_date'] = $credit_release_date;
                    $order['Order']['updated_credit_release_date'] = date('Y-m-d H:i:s');
                    $order['Order']['user_updated_id_credit_release_date'] = CakeSession::read("Auth.User.id");
                }
            }


            if ($this->Order->save($order)) {
                $this->recalculateOrderTransferFees($id);
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
            'fields' => [
                'OrderItem.*',
                'CustomerUserItinerary.*',
                'Benefit.*',
                'BenefitType.name',  // <-- Puxar o nome do tipo
                'Order.*',
                'CustomerUser.*',
                'StatusOutcome.name',
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
                    'table' => 'benefit_types',  // Nome da tabela
                    'alias' => 'BenefitType',
                    'type' => 'LEFT',
                    'conditions' => [
                        'BenefitType.id = Benefit.benefit_type_id'
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
            ]
        ]];

        $condition = ["and" => ['Order.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $_GET['q'] . "%", 'CustomerUser.cpf LIKE' => "%" . $_GET['q'] . "%", 'Benefit.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $items = $this->Paginator->paginate('OrderItem', $condition);

        $progress = 1;
        $condicao_pagamento = $this->request->data['Order']['condicao_pagamento'];
        $pedido_complementar = $this->request->data['Order']['pedido_complementar'];

        $hide_payment_confirmed = false;
        $hide_credit_release = false;
        $hide_processing = false;

        if ($condicao_pagamento == 2 && $pedido_complementar == 1) {
            // Ocultar "Pagamento Confirmado" e "Liberação Créditos"
            $hide_payment_confirmed = true;
            $hide_credit_release = true;
        } elseif ($condicao_pagamento == 2 && $pedido_complementar == 2) {
            // Ocultar "Pagamento Confirmado" e "Em Processamento"
            $hide_payment_confirmed = true;
            $hide_processing = true;
        }

        // Ajustar progress baseado no status
        switch ($order['Order']['status_id']) {
            case 83: // Início
                $progress = 1;
                break;
            case 84: // Aguardando Pagamento
                $progress = 3;
                break;
            case 85: // Pagamento Confirmado (se não oculto) / Em Processamento (se pagamento oculto)
                if ($hide_payment_confirmed) {
                    $progress = 5; // Pular para próxima etapa visível
                } else {
                    $progress = 5;
                }
                break;
            case 86: // Em Processamento (se não oculto) / Aguardando Liberação (se processamento oculto)
                if ($hide_processing) {
                    $progress = 7; // Pular para próxima etapa visível
                } else {
                    $progress = 7;
                }
                break;
            case 104: // Aguardando Liberação (se não oculto) / Em Faturamento
                if ($hide_credit_release) {
                    $progress = 9; // Pular para próxima etapa visível
                } else {
                    $progress = 9;
                }
                break;
            case 115: // Em Faturamento
                $progress = 11;
                break;
            case 87: // Finalizado
                $progress = 12;
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

        $v_cond_pagamento = "";
        if ($order['Order']['condicao_pagamento'] == 1) {
            $v_cond_pagamento = "Pré pago";
        } elseif ($order['Order']['condicao_pagamento'] == 2) {
            $v_cond_pagamento = "Faturado";
        }

        $order_balances_total = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $id, "OrderBalance.tipo" => 1], 'fields' => 'SUM(OrderBalance.total) as total']);

        $this->Order->recursive = 0;

        $outcome = $this->Outcome->find('first', ['conditions' => ['Outcome.order_id' => $id]]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => '', 'Alterar Pedido' => ''];

        $permObsPedido = $this->Permission->check(85, "escrita");
        $permDtLibCredito = $this->Permission->check(87, "escrita");

        $this->set("form_action", "edit");

        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items', 'progress', 'v_is_partial', 'v_cond_pagamento'));
        $this->set(compact('outcome', 'suppliersCount', 'usersCount', 'income', 'benefits', 'gerarNota', 'benefit_type_desc', 'order_balances_total', 'next_order', 'prev_order'));
        $this->set(compact('hide_payment_confirmed', 'hide_credit_release', 'hide_processing', 'condicao_pagamento', 'permObsPedido', 'permDtLibCredito'));

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
            
            $order_status_id = 84;
            if ($order['Order']['condicao_pagamento'] == 2) {
                if ($order['Order']['pedido_complementar'] == 1) {
                    $order_status_id = 86;
                } else {
                    $order_status_id = 104;
                }
            }

            $this->Order->save([
                'Order' => [
                    'id' => $id,
                    'status_id' => $order_status_id,
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

    public function change_status($id = null, $status = null)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");

        $id = (int)$id;
        $status = (int)$status;

        if (!$id || !$status) {
            $this->Flash->set(__('Parâmetros inválidos'), ['params' => ['class' => 'alert alert-danger']]);
            return $this->redirect($this->referer());
        }
        
        $this->Order->id = $id;

        if (!$this->Order->exists()) {
            $this->Flash->set(__('Pedido não encontrado'), ['params' => ['class' => 'alert alert-danger']]);
            return $this->redirect($this->referer());
        }

        $old_status = $this->Order->read();

        $data = ['Order' => ['status_id' => $status]];

        if ($this->Order->save($data, ['validate' => false])) {
            $this->Flash->set(__('Status alterado com sucesso'), ['params' => ['class' => 'alert alert-success']]);
        } else {
            $this->Flash->set(__('Não foi possível alterar o status'), ['params' => ['class' => 'alert alert-danger']]);
        }

        $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        return $this->redirect(['action' => 'index/?' . $qs]);
    }

    public function change_status_cancel($id = null, $status = null)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");

        $id = (int)$id;
        $status = (int)$status;

        if (!$id || !$status) {
            $this->Flash->set(__('Parâmetros inválidos'), ['params' => ['class' => 'alert alert-danger']]);
            return $this->redirect($this->referer());
        }
        
        $this->Order->id = $id;

        if (!$this->Order->exists()) {
            $this->Flash->set(__('Pedido não encontrado'), ['params' => ['class' => 'alert alert-danger']]);
            return $this->redirect($this->referer());
        }

        $old_status = $this->Order->read();

        $data = ['Order' => ['status_id' => $status]];

        if ($this->Order->save($data, ['validate' => false])) {            
            $this->Income->updateAll(
                [
                    'Income.status_id' => 18,
                    'Income.updated' => 'current_timestamp',
                    'Income.user_updated_id' => CakeSession::read("Auth.User.id"),
                    'Income.usuario_id_cancelamento' => CakeSession::read("Auth.User.id")
                ],
                ['Income.order_id' => $id]
            );

            $income_id = $this->Income->find('list', [
                'conditions' => ['Income.order_id' => $id],
                'fields' => ['Income.id', 'Income.id']
            ]);

            if (!empty($income_id)) {
                $this->CnabItem->updateAll(
                    [
                        'CnabItem.status_id' => 63,
                        'CnabItem.updated' => 'current_timestamp',
                        'CnabItem.user_updated_id' => CakeSession::read("Auth.User.id")
                    ],
                    ['CnabItem.income_id' => $income_id]
                );
            }

            $this->Flash->set(__('Status alterado com sucesso'), ['params' => ['class' => 'alert alert-success']]);
        } else {
            $this->Flash->set(__('Não foi possível alterar o status'), ['params' => ['class' => 'alert alert-danger']]);
        }

        $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        return $this->redirect(['action' => 'index/?' . $qs]);
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

        $proposal = $this->Order->getProposalForOrder($order['Order']['customer_id'], $order['Order']['economic_group_id']);

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
        $this->recalculateOrderTransferFees($orderId);
        $this->Order->reProcessAmounts($orderId);
        $this->Order->reprocessFirstOrder($orderId);

        $this->Flash->set(__('Beneficiário incluído com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $orderId]);
    }

    public function upload_user_csv()
    {
        $orderId = $this->request->data['order_id'];
        $customerId = $this->request->data['customer_id'];

        $order = $this->Order->findById($orderId);

        $proposal = $this->Order->getProposalForOrder($order['Order']['customer_id'], $order['Order']['economic_group_id']);

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
        $this->recalculateOrderTransferFees($orderId);
        $this->Order->reProcessAmounts($orderId);
        $this->Order->reprocessFirstOrder($orderId);

        $this->Flash->set(__('Beneficiário(s) incluído(s) com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $orderId]);
    }

    public function upload_saldo_csv()
    {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        ignore_user_abort(true);
        
        $orderId = $this->request->data['order_id'];
        $customerId = $this->request->data['customer_id'];

        $dataSource = $this->OrderBalance->getDataSource();
        $dataSource->begin();
        
        try {
            $ret = $this->parseCSVSaldo($customerId, $orderId, $this->request->data['file']['tmp_name']);

            if (!$ret['success']) {
                throw new Exception($ret['error']);
            }

            $cancelData = [];
            $benefitCache = $this->buildBenefitCache();

            foreach ($ret['data'] as $data) {
                if ($data['tipo']) {
                    $cancelData[] = [
                        'order_id' => $orderId,
                        'tipo' => $data['tipo'],
                        'order_item_id' => $data['order_item_id']
                    ];
                }
            }

            if (!empty($cancelData)) {
                $this->OrderBalance->batchCancelBalances($cancelData, CakeSession::read("Auth.User.id"));
            }

            if (!empty($ret['data'])) {
                $processedData = array_map(function($item) use ($orderId) {
                    $item['order_id'] = $orderId;
                    return $item;
                }, $ret['data']);
                
                $this->bulkInsertOrderBalances($processedData, $benefitCache);
            }

            $this->OrderBalance->update_order_item_saldo($orderId, CakeSession::read("Auth.User.id"));

            $file = new File($this->request->data['file']['name']);
            $dir = new Folder(APP . "webroot/files/order_balances/" . $orderId . "/", true);

            $this->Uploader->up($this->request->data['file'], $dir->path);

            $dataSource->commit();
            
            $this->Flash->set(__('Saldos incluídos com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } catch (Exception $e) {
            $dataSource->rollback();
            $this->Flash->set(__('Erro ao processar arquivo: ') . $e->getMessage(), ['params' => ['class' => "alert alert-danger"]]);
        }
        
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
            $cpfToValidate = str_pad($cpfToValidate, 11, '0', STR_PAD_LEFT);

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

            $unitPrice = $row[1];
            $benefitId = $row[3];            

            $benefit = $this->Benefit->find('first', [
                'conditions' => [
                    'Benefit.code' => $benefitId,
                    'Benefit.data_cancel' => '1901-01-01 00:00:00'
                ],
                'fields' => ['Benefit.id', 'Benefit.is_variable', 'Benefit.unit_price']
            ]);
            
            $is_variable = (int)$benefit['Benefit']['is_variable'] === 1;

            if ($is_variable) {
                $unitPrice = $this->priceFormatBeforeSave($unitPrice);
            } else {
                $unitPrice = $benefit['Benefit']['unit_price_not_formated'];
            }
            
            $workingDays = $row[2];
            $benefitId = $row[3];
            $quantity = $row[4];
            $unitPriceMapping[$existingUser['CustomerUser']['id']][] = ['unitPrice' => $unitPrice, 'workingDays' => $workingDays, 'quantity' => $quantity, 'benefitId' => $benefitId];

            $customerUsersIds[] = $existingUser['CustomerUser']['id'];

            $line++;
        }

        return ['customerUsersIds' => $customerUsersIds, 'unitPriceMapping' => $unitPriceMapping];
    }

    private function parseCSVSaldo($customerId, $orderId, $tmpFile)
    {
        if (!file_exists($tmpFile) || !is_readable($tmpFile)) {
            return ['success' => false, 'error' => 'Arquivo não encontrado ou não legível.'];
        }

        $csv = Reader::createFromPath($tmpFile, 'r');
        $csv->setDelimiter(';');
        
        $records = $csv->getRecords();
        $line = 0;
        $data = [];
        $userCache = [];

        foreach ($records as $row) {
            if ($line == 0 || empty($row[0]) || count($row) < 7) {
                $line++;
                continue;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);
            
            if (empty($cpf)) {
                $line++;
                continue;
            }

            $userCacheKey = $orderId . '_' . $cpf;
            if (!isset($userCache[$userCacheKey])) {
                $existingUser = $this->OrderBalance->find_user_order_items($orderId, $cpf);
                $userCache[$userCacheKey] = isset($existingUser[0]['u']) ? $existingUser[0]['u']['id'] : null;
            }

            $total = str_replace(["R$", " "], "", $row[2]);

            $data[] = [
                'customer_user_id' => $userCache[$userCacheKey],
                'document' => $row[0],
                'benefit_code' => $row[1],
                'total' => $total,
                'pedido_operadora' => $row[3],
                'order_item_id' => $row[4],
                'tipo' => $row[5],
                'observacao' => isset($row[6]) ? $row[6] : '',
            ];

            $line++;
        }

        return [
            'success' => true,
            'data' => $data,
            'processedLines' => $line - 1
        ];
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

        // Transfer fee will be calculated properly in recalculateOrderTransferFees() with full order context
        $orderItem['OrderItem']['transfer_fee'] = 0;
        $orderItem['OrderItem']['calculation_details_log'] = json_encode(['note' => 'Will be calculated with full order context']);

        $proposal = $this->Order->getProposalForOrder($orderItem['Order']['customer_id'], $orderItem['Order']['economic_group_id']);
        
        $commissionPerc = 0;
        if (!empty($proposal)) {
            $commissionPerc = $this->getCommissionPerc($benefit['Benefit']['benefit_type_id'], $proposal);
        }

        $orderItem['OrderItem']['commission_fee'] = $commissionPerc > 0 ? $orderItem['OrderItem']['subtotal'] * ($commissionPerc / 100) : 0;

        // Parse all values to ensure they are numeric
        $subtotal = $this->parseFormattedNumber($orderItem['OrderItem']['subtotal']);
        $transferFeeNumeric = $this->parseFormattedNumber($orderItem['OrderItem']['transfer_fee']);
        $commissionFeeNumeric = $this->parseFormattedNumber($orderItem['OrderItem']['commission_fee']);
        
        $orderItem['OrderItem']['total'] = $subtotal + $transferFeeNumeric + $commissionFeeNumeric;

        $this->OrderItem->id = $itemId;
        $this->OrderItem->save($orderItem);

        $orderItem = $this->OrderItem->findById($itemId, null, null, -1);

        // Recalculate transfer fees for all supplier types
        $this->recalculateOrderTransferFees($orderItem['OrderItem']['order_id']);

        $this->Order->id = $orderItem['OrderItem']['order_id'];
        $this->Order->reProcessAmounts($orderItem['OrderItem']['order_id']);
        $this->Order->reprocessFirstOrder($orderItem['OrderItem']['order_id']);

        // Get updated order item data after recalculation
        $updatedOrderItem = $this->OrderItem->findById($itemId, null, null, -1);
        $order = $this->Order->findById($orderItem['OrderItem']['order_id']);

        $pedido_subtotal = $order['Order']['subtotal'];
        $pedido_transfer_fee = $order['Order']['transfer_fee'];
        $pedido_commission_fee = $order['Order']['commission_fee'];
        $pedido_total = $order['Order']['total'];

        echo json_encode([
            'success' => true,
            'subtotal' => $updatedOrderItem['OrderItem']['subtotal'],
            'transfer_fee' => $updatedOrderItem['OrderItem']['transfer_fee'],
            'commission_fee' => $updatedOrderItem['OrderItem']['commission_fee'],
            'total' => $updatedOrderItem['OrderItem']['total'],
            'calculation_details_log' => $updatedOrderItem['OrderItem']['calculation_details_log'],
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
        $this->recalculateOrderTransferFees($orderId);
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
        $working_days = $this->request->data['CustomerUserItinerary']['working_days'];

        $order = $this->Order->findById($orderId);

        $proposal = $this->Order->getProposalForOrder($order['Order']['customer_id'], $order['Order']['economic_group_id']);

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

            $this->processItineraries($customerItineraries, $orderId, $working_days, $order['Order']['order_period_from'], $order['Order']['order_period_to'], 1, $proposal);

            $this->Order->id = $orderId;
            $this->recalculateOrderTransferFees($orderId);
            $this->Order->reProcessAmounts($orderId);
            $this->Order->reprocessFirstOrder($orderId);

            $this->Flash->set(__('Itinerário adicionado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect('/orders/edit/' . $orderId);
        } else {
            $this->Flash->set(__('Itinerário não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
        }
    }

    private function processConsolidated($customerId, $workingDays, $period_from, $period_to, $is_partial, $credit_release_date, $working_days_type, $grupo_especifico, $benefit_type, $proposal, $pedido_complementar, $condicao_pagamento, $prazo, $due_date)
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
                'condicao_pagamento' => $condicao_pagamento,
                'prazo' => $prazo,
                'due_date' => $due_date,
            ];

            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();

                if ($is_partial == 2) {
                    $this->processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type, $proposal);
                }

                $this->Order->id = $orderId;
                $this->recalculateOrderTransferFees($orderId);
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
                'sum(OrderItem.transfer_fee) as transfer_fee',
                'sum(OrderItem.commission_fee) as commission_fee',
                'sum(OrderItem.total) as total',
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
                    WHERE o.id = OrderItem.outcome_id
                            AND o.supplier_id = Supplier.id
                            AND o.data_cancel = '1901-01-01 00:00:00'
                ) AS count_outcomes",
                "(SELECT SUM(o.valor_total) AS valor_total
                    FROM outcomes o
                    WHERE o.id = OrderItem.outcome_id
                            AND o.supplier_id = Supplier.id
                            AND o.data_cancel = '1901-01-01 00:00:00'
                ) AS total_outcomes",
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
                'StatusOutcome.*',
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
            'group' => ['OrderItem.id']

        ]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Operadores' => '', 'Detalhes' => ''];
        $this->set(compact('action', 'breadcrumb', 'id', 'supplier_id', 'suppliersAll'));
    }

    public function operadoras_detalhes_export($id, $supplier_id)
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        
        $suppliersAll = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id, 'Supplier.id' => $supplier_id],
            'fields' => [
                'Order.id',
                'Supplier.id',
                'Supplier.razao_social',
                'Benefit.name',
                'CustomerUser.name',
                'OrderItem.*',
                'StatusOutcome.*',
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
            'group' => ['OrderItem.id']
        ]);

        $nome = 'operadoras_detalhes_' . $id . '_' . $supplier_id . '_' . date('d_m_Y_H_i_s') . '.xlsx';
        
        $this->ExcelGenerator->gerarOperadorasDetalhes($nome, $suppliersAll);
        $this->redirect('/files/excel/' . $nome);
    }

    public function liberar_faturamento($id)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;

        $this->Order->save([
            'Order' => [
                'id' => $id,
                'status_id' => 115,
                'user_updated_id' => CakeSession::read("Auth.User.id"),
                'updated' => date('Y-m-d H:i:s'),
            ]
        ]);

        $this->Flash->set(__('O status foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);

        $this->redirect(['action' => 'edit/' . $id]);
    }

    public function confirma_pagamento($id)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;

        $this->Order->recursive = -1;
        $this->Order->atualizarStatusPagamento($id);

        $order = $this->Order->find('first', ['fields' => ['Order.status_id'], 'conditions' => ['Order.id' => $id], 'recursive' => -1]); 

        if (in_array($order['Order']['status_id'], [85, 87])) {
            $this->Order->save([
                'Order' => [
                    'id' => $id,
                    'status_id' => 87,
                    'end_date' => date('d/m/Y'),
                ]
            ]);
        }

        $this->Flash->set(__('O Pagamento foi confirmado com sucesso'), ['params' => ['class' => "alert alert-success"]]);

        $this->redirect(['action' => 'edit/' . $id]);
    }

    public function confirma_faturamento($id)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;

        $this->Order->save([
            'Order' => [
                'id' => $id,
                'status_id' => 115,
                'user_updated_id' => CakeSession::read("Auth.User.id"),
                'updated' => date('Y-m-d H:i:s'),
            ]
        ]);

        $this->Flash->set(__('O status foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);

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
                'count(CustomerUserItinerary.quantity) as qtd',
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

        if (!empty($_GET['cond_pag'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.condicao_pagamento' => $_GET['cond_pag']]);
        }

        $orders = $this->Order->find('all', [
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
                $commissionPerc = $proposal['transport_adm_fee'];
                break;
            case 4:
                $commissionPerc = $proposal['meal_adm_fee'];
                break;
            case 8:
                $commissionPerc = $proposal['fuel_adm_fee'];
                break;
            case 5:
            case 6:
            case 7:
            case 10:
                $commissionPerc = $proposal['multi_card_adm_fee'];
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

    public function baixar_beneficiarios_pix($id)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;

        $nome = 'beneficiarios_pedido_pix_' . $id . '.xlsx';

        $data = $this->CustomerUser->find_pedido_beneficiarios_info($id, 'sim');

        $this->ExcelGenerator->gerarExcelPedidosBeneficiariosPIX($nome, $data);

        $this->redirect("/files/excel/" . $nome);
    }

    public function baixar_beneficiarios_conta_bancaria($id)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $view = new View($this, false);
        $view->layout = false;

        $nome = 'beneficiarios_pedido_conta_bancaria_' . $id . '.xlsx';

        $data = $this->CustomerUser->find_pedido_beneficiarios_info($id, 'nao');

        $this->ExcelGenerator->gerarExcelPedidosBeneficiariosContaBancaria($nome, $data);

        $this->redirect("/files/excel/" . $nome);
    }

    public function gerar_remessa_pix($id) {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $statusProcess      = isset($this->request->data['status_processamento']) ? $this->request->data['status_processamento'] : null;
        $pedido_operadora   = isset($this->request->data['pedido_operadora']) ? $this->request->data['pedido_operadora'] : null;
        $data_entrega       = isset($this->request->data['data_entrega']) ? $this->request->data['data_entrega'] : null;
        $data_vencimento    = isset($this->request->data['data_vencimento']) ? $this->request->data['data_vencimento'] : null;        
        $data_pagamento     = isset($this->request->data['data_pagamento']) ? $this->request->data['data_pagamento'] : null;
        $motivo             = isset($this->request->data['motivo']) ? $this->request->data['motivo'] : null;

        $orderItems = $this->OrderItem->find('all', [
            'fields' => ['OrderItem.id', 'CustomerUser.id', 'CustomerUser.name'],
            'conditions' => [
                'OrderItem.order_id' => $id,
                'OrderItem.outcome_id' => null,
            ],
        ]);

        $benefitValid = [];
        foreach ($orderItems as $item) {
            $data = [
                'OrderItem' => [
                    'id' => $item['OrderItem']['id'],
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

            $existingBankAccount = $this->CustomerUserBankAccount->find('first', [
                'conditions' => [
                    'CustomerUserBankAccount.customer_user_id' => $item['CustomerUser']['id'],
                    'CustomerUserBankAccount.data_cancel' => '1901-01-01 00:00:00'
                ]
            ]);

            if (empty($existingBankAccount)) {
                if (!in_array($item['CustomerUser']['name'], $benefitValid)) {
                    $benefitValid[] = $item['CustomerUser']['name'];
                }
            }
        }

        $orderItems = $this->OrderItem->find('all', [
            'fields' => ['OrderItem.id', 'Order.id', 'Supplier.id', 'SUM(OrderItem.subtotal) as subtotal', 'SUM(OrderItem.transfer_fee) as transfer_fee'],
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
            'conditions' => [
                'OrderItem.order_id' => $id,
                'OrderItem.outcome_id' => null,
            ],
            'group' => ['Supplier.id'],
        ]);
        
        $total = 0;
        foreach ($orderItems as $item) {
            $total++;

            $valor_total = ($item[0]['subtotal'] + $item[0]['transfer_fee']);

            $outcome = [];
            $outcome['Outcome']['supplier_id'] = $item['Supplier']['id'];
            $outcome['Outcome']['resale_id'] = 1;
            $outcome['Outcome']['doc_num'] = $id;
            $outcome['Outcome']['parcela'] = 1;
            $outcome['Outcome']['status_id'] = 11;
            $outcome['Outcome']['name'] = 'Pagamento a Operadoras';
            $outcome['Outcome']['valor_multa'] = 0;
            $outcome['Outcome']['valor_desconto'] = 0;
            $outcome['Outcome']['valor_bruto'] = number_format($valor_total, 2, ',', '.');
            $outcome['Outcome']['valor_total'] = number_format($valor_total, 2, ',', '.');
            $outcome['Outcome']['bank_account_id'] = 4;
            $outcome['Outcome']['vencimento'] = $data_vencimento;
            $outcome['Outcome']['expense_id'] = 1;
            $outcome['Outcome']['cost_center_id'] = 116;
            $outcome['Outcome']['plano_contas_id'] = 1;
            $outcome['Outcome']['recorrencia'] = 2;
            $outcome['Outcome']['payment_method'] = 11;
            $outcome['Outcome']['data_competencia'] = date('01/m/Y');
            $outcome['Outcome']['data_pagamento'] = date('Y-m-d', strtotime(str_replace('/', '-', $data_pagamento)));
            $outcome['Outcome']['user_creator_id'] = CakeSession::read("Auth.User.id");
            
            $this->Outcome->create();
            $this->Outcome->save($outcome);
            
            $outcome_id = $this->Outcome->id;
            
            $outcomeOrder = [];
            $outcomeOrder['OutcomeOrder']['outcome_id'] = $outcome_id;
            $outcomeOrder['OutcomeOrder']['order_id'] = $id;
            
            $this->OutcomeOrder->create();
            $this->OutcomeOrder->save($outcomeOrder);
            
            // Atualizar todos os OrderItems deste supplier com o outcome_id
            $this->OrderItem->updateAll(
                ['OrderItem.outcome_id' => $outcome_id],
                [
                    'OrderItem.order_id' => $id,
                    'OrderItem.outcome_id' => null,
                    'EXISTS (SELECT 1 FROM benefits b 
                            INNER JOIN suppliers s ON s.id = b.supplier_id 
                            WHERE b.id = CustomerUserItinerary.benefit_id 
                            AND s.id = ' . $item['Supplier']['id'] . ')'
                ]
            );
        }

        if (!empty($benefitValid)) {
            sort($benefitValid);
            foreach ($benefitValid as $nome) {
                $this->Flash->set(__('Beneficiário ' . $nome . ' não tem dados bancário.'), ['params' => ['class' => "alert alert-warning"]]);
            }
        }

        if ($total > 0) {
            $this->Flash->set(__('Remessa gerada com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
        } else {
            $this->Flash->set(__('Já existe uma remessa com esse pedido.'), ['params' => ['class' => "alert alert-danger"]]);
        }

        $this->redirect(['action'=> 'edit', $id]);
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
            'fields' => ['OrderItem.*', 'CustomerUserItinerary.*', 'Benefit.*', 'Order.*', 'CustomerUser.*', 'Supplier.*', 'StatusOutcome.*'],
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
            ]
        ]];

        $condition = ["and" => ['Order.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $_GET['q'] . "%", 'CustomerUser.cpf LIKE' => "%" . $_GET['q'] . "%", 'Benefit.name LIKE' => "%" . $_GET['q'] . "%", 'Benefit.code LIKE' => "%" . $_GET['q'] . "%", 'Supplier.nome_fantasia LIKE' => "%" . $_GET['q'] . "%", 'OrderItem.status_processamento LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['sup']) and $_GET['sup'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Supplier.id' => $_GET['sup']]);
        }

        if (isset($_GET['stp']) and $_GET['stp'] != '') {
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

        $conditionsJson = base64_encode(json_encode($condition));

        $action = 'Compras';
        $breadcrumb = ['Cadastros' => '', 'Compras' => '', 'Alterar Compras' => ''];

        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items', 'items_total', 'conditionsJson'));
    }

    public function alter_item_status_processamento()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");

        $order_id           = isset($this->request->data['order_id']) ? $this->request->data['order_id'] : null;
        $statusProcess      = isset($this->request->data['v_status_processamento']) ? $this->request->data['v_status_processamento'] : null;
        $pedido_operadora   = isset($this->request->data['v_pedido_operadora']) ? $this->request->data['v_pedido_operadora'] : null;
        $data_entrega       = isset($this->request->data['v_data_entrega']) ? $this->request->data['v_data_entrega'] : null;
        $data_vencimento    = isset($this->request->data['v_data_vencimento']) ? $this->request->data['v_data_vencimento'] : null;
        $forma_pagamento    = isset($this->request->data['v_forma_pagamento']) ? $this->request->data['v_forma_pagamento'] : null;
        $motivo             = isset($this->request->data['v_motivo']) ? $this->request->data['v_motivo'] : null;
        $observacoes        = isset($this->request->data['v_observacoes']) ? $this->request->data['v_observacoes'] : null;

        $file_item          = isset($_FILES['file_item']) ? $_FILES['file_item'] : null;
        $file_repasse       = isset($_FILES['file_repasse']) ? $_FILES['file_repasse'] : null;
                
        $orderItemIds       = isset($this->request->data['orderItemIds']) ? json_decode($this->request->data['orderItemIds'], true) : null;
        $notOrderItemIds    = isset($this->request->data['notOrderItemIds']) ? json_decode($this->request->data['notOrderItemIds'], true) : null;
        
        $condition          = json_decode(base64_decode($this->request->data('conditions')), true);
        
        if (!empty($orderItemIds) && is_array($orderItemIds)) {
            $condition['and'] = array_merge($condition['and'], ['OrderItem.id' => $orderItemIds]);
        }
        
        if (!empty($notOrderItemIds) && is_array($notOrderItemIds)) {
            $condition['and'] = array_merge($condition['and'], ['OrderItem.id !=' => $notOrderItemIds]);
        }

        if (!isset($condition['and']['Order.id'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.id' => $order_id]);
        }
        
        $condition['and'] = array_merge($condition['and'], ['OrderItem.outcome_id' => null]);

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

        $itemOrderId = [];
        foreach ($items as $item) {
            $itemOrderId[] = $item['OrderItem']['id'];

            $orderItem = $this->OrderItem->findById($item['OrderItem']['id']);

            $this->LogOrderItemsProcessamento->logProcessamento($orderItem);

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

            if ($statusProcess == 'PAGAMENTO_REALIZADO') {
                if (in_array($orderItem['OrderItem']['status_processamento'], ['CADASTRO_INCONSISTENTE', 'CARTAO_NOVO_CREDITO_INCONSISTENTE', 'CREDITO_INCONSISTENTE'])) {
                    $subtotal = $orderItem['OrderItem']['subtotal_not_formated'];
                    if ($subtotal > 0) {
                        $subtotal = $subtotal * -1;
                    }

                    $orderBalanceData = [
                        'order_id' => $orderItem['Order']['id'],
                        'order_item_id' => $orderItem['OrderItem']['id'],
                        'customer_user_id' => $orderItem['CustomerUser']['id'],
                        'benefit_id' => $orderItem['CustomerUserItinerary']['benefit_id'],
                        'document' => $orderItem['CustomerUser']['cpf'],
                        'total' => $subtotal,
                        'pedido_operadora' => $pedido_operadora,
                        'observacao' => $motivo,
                        'tipo' => 2,
                        'created' => date('Y-m-d H:i:s'),
                        'user_created_id' => CakeSession::read("Auth.User.id")
                    ];

                    $this->OrderBalance->create();
                    $this->OrderBalance->save($orderBalanceData);
                }
            }

            $this->OrderItem->save($data);

            if (in_array($statusProcess, ['CARTAO_NOVO_CREDITO_INCONSISTENTE', 'CREDITO_INCONSISTENTE'])) {
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
        
        if (in_array($statusProcess, ['GERAR_PAGAMENTO', 'CARTAO_NOVO_PROCESSADO'])) {
            $orderItems = $this->OrderItem->find('all', [
                'fields' => ['OrderItem.id', 'Order.id', 'Supplier.id', 'SUM(OrderItem.subtotal) as subtotal', 'SUM(OrderItem.transfer_fee) as transfer_fee'],
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
                'conditions' => [
                    'OrderItem.id' => $itemOrderId,
                    'OrderItem.outcome_id' => null,
                ],
                'group' => ['Supplier.id'],
            ]);

            foreach ($orderItems as $item) {
                $valor_total = ($item[0]['subtotal'] + $item[0]['transfer_fee']);
                
                $outcome = [];
                $outcome['Outcome']['supplier_id'] = $item['Supplier']['id'];
                $outcome['Outcome']['resale_id'] = 1;
                $outcome['Outcome']['doc_num'] = $item['Order']['id'];
                $outcome['Outcome']['parcela'] = 1;
                $outcome['Outcome']['status_id'] = 11;
                $outcome['Outcome']['name'] = 'Pagamento a Operadoras';
                $outcome['Outcome']['valor_multa'] = 0;
                $outcome['Outcome']['valor_desconto'] = 0;
                $outcome['Outcome']['valor_bruto'] = number_format($valor_total, 2, ',', '.');
                $outcome['Outcome']['valor_total'] = number_format($valor_total, 2, ',', '.');
                $outcome['Outcome']['bank_account_id'] = 4;
                $outcome['Outcome']['vencimento'] = $data_vencimento;
                $outcome['Outcome']['payment_method'] = $forma_pagamento;
                $outcome['Outcome']['observation'] = $observacoes;
                $outcome['Outcome']['expense_id'] = 1;
                $outcome['Outcome']['cost_center_id'] = 116;
                $outcome['Outcome']['plano_contas_id'] = 1;
                $outcome['Outcome']['recorrencia'] = 2;
                $outcome['Outcome']['data_competencia'] = date('01/m/Y');
                $outcome['Outcome']['user_creator_id'] = CakeSession::read("Auth.User.id");
                
                $this->Outcome->create();
                $this->Outcome->save($outcome);
                
                $outcome_id = $this->Outcome->id;

                if ($file_item) {
                    $doc_outcome = [];
                    $doc_outcome['Docoutcome']['outcome_id'] = $outcome_id;
                    $doc_outcome['Docoutcome']['file'] = $file_item;
                    $doc_outcome['Docoutcome']['status_id'] = 1;
                    $doc_outcome['Docoutcome']['user_creator_id'] = CakeSession::read('Auth.User.id');

                    $this->Docoutcome->create();
                    $this->Docoutcome->save($doc_outcome);
                }

                if ($file_repasse) {
                    $this->Outcome->create();
                    $this->Outcome->save($outcome);
                
                    $outc_id = $this->Outcome->id;

                    $doc_outcome = [];
                    $doc_outcome['Docoutcome']['outcome_id'] = $outc_id;
                    $doc_outcome['Docoutcome']['file'] = $file_repasse;
                    $doc_outcome['Docoutcome']['status_id'] = 1;
                    $doc_outcome['Docoutcome']['user_creator_id'] = CakeSession::read('Auth.User.id');

                    $this->Docoutcome->create();
                    $this->Docoutcome->save($doc_outcome);
                }
                
                // Buscar TODOS os pedidos deste supplier nos itens selecionados
                $ordersForSupplier = $this->OrderItem->find('all', [
                    'fields' => ['DISTINCT Order.id'],
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
                    'conditions' => [
                        'OrderItem.id' => $itemOrderId,
                        'OrderItem.outcome_id' => null,
                        'Supplier.id' => $item['Supplier']['id']
                    ]
                ]);
                
                // Salvar na tabela OutcomeOrder para cada pedido deste supplier
                foreach ($ordersForSupplier as $order) {
                    $outcomeOrder = [];
                    $outcomeOrder['OutcomeOrder']['outcome_id'] = $outcome_id;
                    $outcomeOrder['OutcomeOrder']['order_id'] = $order['Order']['id'];
                    
                    $this->OutcomeOrder->create();
                    $this->OutcomeOrder->save($outcomeOrder);

                    if (isset($outc_id)) {
                        $outcomeOrder = [];
                        $outcomeOrder['OutcomeOrder']['outcome_id'] = $outc_id;
                        $outcomeOrder['OutcomeOrder']['order_id'] = $order['Order']['id'];
                        
                        $this->OutcomeOrder->create();
                        $this->OutcomeOrder->save($outcomeOrder);
                    }
                }
                
                // Atualizar todos os OrderItems deste supplier com o outcome_id
                $this->OrderItem->updateAll(
                    ['OrderItem.outcome_id' => $outcome_id],
                    [
                        'OrderItem.id' => $itemOrderId,
                        'OrderItem.outcome_id' => null,
                        'EXISTS (SELECT 1 FROM benefits b 
                                INNER JOIN suppliers s ON s.id = b.supplier_id 
                                WHERE b.id = CustomerUserItinerary.benefit_id 
                                AND s.id = ' . $item['Supplier']['id'] . ')'
                    ]
                );
            }
        }

        echo json_encode(['success' => true]);
    }

    public function get_total_items() 
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        $this->Permission->check(91, "escrita") ? "" : $this->redirect("/not_allowed");

        $order_id           = isset($this->request->data['order_id']) ? $this->request->data['order_id'] : null;

        $orderItemIds       = isset($this->request->data['orderItemIds']) ? json_decode($this->request->data['orderItemIds'], true) : null;
        $notOrderItemIds    = isset($this->request->data['notOrderItemIds']) ? json_decode($this->request->data['notOrderItemIds'], true) : null;
        
        $condition          = json_decode(base64_decode($this->request->data('conditions')), true);
        
        if (!empty($orderItemIds) && is_array($orderItemIds)) {
            $condition['and'] = array_merge($condition['and'], ['OrderItem.id' => $orderItemIds]);
        }
        
        if (!empty($notOrderItemIds) && is_array($notOrderItemIds)) {
            $condition['and'] = array_merge($condition['and'], ['OrderItem.id !=' => $notOrderItemIds]);
        }

        if (!isset($condition['and']['Order.id'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.id' => $order_id]);
        }
        
        $condition['and'] = array_merge($condition['and'], ['OrderItem.outcome_id' => null]);

        $items = $this->OrderItem->find('all', [
            'conditions' => $condition,
            'group' => ['Supplier.id', 'Supplier.tipo_boleto'],
            'fields' => [
                'Supplier.id',
                'Supplier.tipo_boleto',
                'SUM(COALESCE(OrderItem.subtotal, 0) - COALESCE(OrderItem.saldo, 0)) as soma_subtotal',
                'SUM(COALESCE(OrderItem.transfer_fee, 0) - COALESCE(OrderItem.saldo_transfer_fee, 0)) as soma_transfer_fee',
                'SUM((COALESCE(OrderItem.subtotal, 0) - COALESCE(OrderItem.saldo, 0)) + (COALESCE(OrderItem.transfer_fee, 0) - COALESCE(OrderItem.saldo_transfer_fee, 0))) as soma_total'
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
            ]
        ]);
        
        $suppliers = [];
        $tipo_boleto = null;
        $soma_subtotal = 0;
        $soma_transfer_fee = 0;
        $soma_total = 0;

        foreach ($items as $result) {
            $suppliers[] = $result['Supplier']['id'];
            $tipo_boleto = $result['Supplier']['tipo_boleto'];
            $soma_subtotal += floatval($result[0]['soma_subtotal']);
            $soma_transfer_fee += floatval($result[0]['soma_transfer_fee']);
            $soma_total += floatval($result[0]['soma_total']);
        }
        
        $valid = count(array_unique($suppliers)) === 1;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'soma_subtotal' => $soma_subtotal,
                'soma_transfer_fee' => $soma_transfer_fee,
                'soma_total' => $soma_total,
                'tipo_boleto' => $tipo_boleto,
                'valid' => $valid,
                'supplier_id' => !empty($suppliers) ? $suppliers[0] : null
            ]
        ]);
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

            // Determine price based on is_variable flag
            if ($is_variable) {
                // Variable benefits use price from spreadsheet
                $unitPriceForm = $this->priceFormatBeforeSave($unitPrice);
            } else {
                // Fixed benefits use default price from benefit table
                $unitPriceForm = $benefit['Benefit']['unit_price_not_formated'];
                $unitPrice = $benefit['Benefit']['unit_price'];
            }

            $idItinerary = 0;
            if (empty($existingItinerary)) {
                // Only create new itinerary if none exists

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
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        ignore_user_abort(true);

        $startTime = microtime(true);
        CakeLog::write('info', 'Starting upload_saldo_csv_all process');

        $dataSource = $this->OrderBalance->getDataSource();
        $dataSource->begin();

        try {
            $parseStart = microtime(true);
            $ret = $this->parseCSVSaldoAll($this->request->data['file']['tmp_name']);
            $parseTime = microtime(true) - $parseStart;
            CakeLog::write('info', "CSV parsing completed in " . number_format($parseTime, 2) . " seconds");

            if (!$ret['success']) {
                throw new Exception($ret['error']);
            }

            $processedData = $ret['data'];
            $groupTpOrder = [];
            $groupOrder = [];
            $cancelData = [];

            $groupingStart = microtime(true);
            foreach ($processedData as $item) {
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
                
                $cancelData[] = [
                    'order_id' => $item['order_id'],
                    'tipo' => $item['tipo'],
                    'order_item_id' => $item['order_item_id']
                ];
            }
            $groupingTime = microtime(true) - $groupingStart;
            CakeLog::write('info', "Data grouping completed in " . number_format($groupingTime, 2) . " seconds");

            if (!empty($cancelData)) {
                $cancelStart = microtime(true);
                $this->OrderBalance->batchCancelBalances($cancelData, CakeSession::read("Auth.User.id"));
                $cancelTime = microtime(true) - $cancelStart;
                CakeLog::write('info', "Batch cancel balances completed in " . number_format($cancelTime, 2) . " seconds");
            }

            if (!empty($processedData)) {
                $insertStart = microtime(true);
                $this->bulkInsertOrderBalances($processedData, $ret['benefitCache']);
                $insertTime = microtime(true) - $insertStart;
                CakeLog::write('info', "Bulk insert order balances completed in " . number_format($insertTime, 2) . " seconds");
            }

            $processedOrders = 0;
            $totalOrders = count($groupOrder);

            $updateStart = microtime(true);
            foreach ($groupOrder as $item) {
                if ($item['order_id']) {
                    $this->OrderBalance->update_order_item_saldo($item['order_id'], CakeSession::read("Auth.User.id"));
                    $processedOrders++;
                    
                    if ($processedOrders % 10 == 0 || $processedOrders == $totalOrders) {
                        CakeLog::write('info', "Updated {$processedOrders}/{$totalOrders} orders");
                    }
                }
            }
            $updateTime = microtime(true) - $updateStart;
            CakeLog::write('info', "Order balance updates completed in " . number_format($updateTime, 2) . " seconds");

            $fileStart = microtime(true);
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
            $fileTime = microtime(true) - $fileStart;
            CakeLog::write('info', "File operations completed in " . number_format($fileTime, 2) . " seconds");

            $dataSource->commit();

            $totalTime = microtime(true) - $startTime;
            CakeLog::write('info', "Total upload_saldo_csv_all process completed in " . number_format($totalTime, 2) . " seconds");

            $this->Flash->set(__('Movimentações incluídas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } catch (Exception $e) {
            $dataSource->rollback();
            $this->Flash->set(__('Erro ao processar arquivo: ') . $e->getMessage(), ['params' => ['class' => "alert alert-danger"]]);
        }
        
        $this->redirect('/reports/importar_movimentacao');
    }

    private function ensureLeadingZeroes($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);


        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        return $cpf;
    }
    
    private function buildBenefitCache()
    {
        $benefits = $this->Benefit->find('all', [
            'fields' => ['Benefit.id', 'Benefit.code'],
            'conditions' => ['Benefit.data_cancel' => '1901-01-01 00:00:00'],
            'recursive' => -1
        ]);
        
        $cache = [];
        foreach ($benefits as $benefit) {
            $cache[$benefit['Benefit']['code']] = $benefit['Benefit']['id'];
        }
        
        return $cache;
    }
    
    private function bulkInsertOrderBalances($data, $benefitCache)
    {
        $batchSize = 500;
        $batches = array_chunk($data, $batchSize);
        $userId = CakeSession::read("Auth.User.id");
        $currentDateTime = date('Y-m-d H:i:s');
        $totalBatches = count($batches);
        $processedBatches = 0;
        
        foreach ($batches as $batch) {
            $values = [];
            $placeholders = [];
            
            foreach ($batch as $item) {
                if (!$item['tipo']) continue;
                
                $benefit_id = isset($benefitCache[$item['benefit_code']]) ? $benefitCache[$item['benefit_code']] : null;
                
                $values = array_merge($values, [
                    $item['order_id'],
                    $item['order_item_id'],
                    $item['customer_user_id'],
                    $benefit_id,
                    $item['document'],
                    $this->OrderBalance->priceFormatBeforeSave($item['total']),
                    $item['pedido_operadora'],
                    $item['tipo'],
                    $item['observacao'],
                    $currentDateTime,
                    $userId
                ]);
                
                $placeholders[] = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            }
            
            if (!empty($placeholders)) {
                $sql = "INSERT INTO order_balances 
                        (order_id, order_item_id, customer_user_id, benefit_id, document, total, 
                         pedido_operadora, tipo, observacao, created, user_created_id) 
                        VALUES " . implode(', ', $placeholders);
                
                try {
                    $this->OrderBalance->query($sql, $values);
                    $processedBatches++;
                    
                    if ($processedBatches % 10 == 0 || $processedBatches == $totalBatches) {
                        CakeLog::write('info', "Processed {$processedBatches}/{$totalBatches} batches");
                    }
                } catch (Exception $e) {
                    CakeLog::write('error', 'Bulk insert failed: ' . $e->getMessage());
                    throw $e;
                }
            }
        }
    }

    private function parseCSVSaldoAll($tmpFile)
    {
        if (!file_exists($tmpFile) || !is_readable($tmpFile)) {
            return ['success' => false, 'error' => 'Arquivo não encontrado ou não legível.'];
        }

        $csv = Reader::createFromPath($tmpFile, 'r');
        $csv->setDelimiter(';');

        $records = $csv->getRecords();
        $header = null;
        $data = [];
        $line = 0;
        $chunkSize = 1000;
        $currentChunk = [];

        $benefitCache = $this->buildBenefitCache();

        // First pass: collect all order IDs from CSV
        $preloadStart = microtime(true);
        $allOrderIds = [];
        $tempRecords = [];
        foreach ($records as $row) {
            if ($line == 0) {
                $header = $row;
                if (count($row) < 8) {
                    return ['success' => false, 'error' => 'Formato de arquivo inválido. Esperado 8 colunas, encontrado ' . count($row)];
                }
                $line++;
                continue;
            }

            if (empty($row[0]) || count($row) < 8) {
                $line++;
                continue;
            }

            $orderId = trim($row[7]);
            if (!empty($orderId) && is_numeric($orderId)) {
                $allOrderIds[$orderId] = true;
            }
            $tempRecords[] = $row;
            $line++;
        }

        // Bulk load all users for all orders
        $userMap = $this->OrderBalance->bulk_load_users_for_orders(array_keys($allOrderIds));
        $preloadTime = microtime(true) - $preloadStart;
        CakeLog::write('info', "Bulk user preload completed in " . number_format($preloadTime, 2) . " seconds for " . count($allOrderIds) . " orders");

        $processedOrderIds = [];
        $lastProgressReport = 0;
        $line = 1; // Reset line counter

        // Second pass: process the actual data using preloaded users
        foreach ($tempRecords as $row) {
            if (empty($row[0]) || count($row) < 8) {
                $line++;
                continue;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);
            $orderId = trim($row[7]);

            if (empty($cpf) || empty($orderId) || !is_numeric($orderId)) {
                CakeLog::write('warning', "Linha {$line}: CPF ou Order ID inválido - CPF: {$row[0]}, Order ID: {$orderId}");
                $line++;
                continue;
            }

            $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

            if (empty($cpf) || !$this->isValidCPF($cpf)) {
                CakeLog::write('warning', "Linha {$line}: CPF com formato inválido: {$cpf}");
                $line++;
                continue;
            }

            // Look up user from preloaded data instead of individual queries
            $userId = null;
            if (isset($userMap[$orderId][$cpf])) {
                $userId = $userMap[$orderId][$cpf];
            } else {
                CakeLog::write('warning', "Linha {$line}: Customer user não encontrado para CPF: {$cpf}, Order ID: {$orderId}");
            }

            $total = str_replace(["R$", " "], "", $row[2]);

            $currentChunk[] = [
                'customer_user_id' => $userId,
                'document' => $row[0],
                'benefit_code' => $row[1],
                'total' => $total,
                'pedido_operadora' => $row[3],
                'order_item_id' => $row[4],
                'tipo' => $row[5],
                'observacao' => $row[6],
                'order_id' => $orderId,
            ];
            
            $processedOrderIds[$orderId] = true;

            if (count($currentChunk) >= $chunkSize) {
                $data = array_merge($data, $currentChunk);
                $currentChunk = [];
                
                if ($line - $lastProgressReport >= 5000) {
                    CakeLog::write('info', "Processed {$line} lines from CSV");
                    $lastProgressReport = $line;
                }
            }

            $line++;
        }
        
        if (!empty($currentChunk)) {
            $data = array_merge($data, $currentChunk);
        }
        
        usort($data, function ($a, $b) {
            return strcmp($a['order_id'], $b['order_id']);
        });

        return [
            'success' => true,
            'data' => $data,
            'benefitCache' => $benefitCache,
            'processedLines' => $line - 1
        ];
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
        $extra_ids = [
            'cost_center_id' => null,
            'customer_department_id' => null,
            'economic_group_id' => null
        ];

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
                    'TRIM(CostCenter.name)' => trim($centroCusto),
                    'CostCenter.customer_id' => $customerId,
                    'CostCenter.data_cancel' => '1901-01-01 00:00:00'
                ]
            ]);

            if (empty($costCenter)) {
                $this->CostCenter->create();
                $this->CostCenter->save([
                    'name' => trim($centroCusto),
                    'customer_id' => $customerId, 
                    'user_creator_id' => CakeSession::read("Auth.User.id"),
                    'data_cancel' => '1901-01-01 00:00:00'
                ]);

                $extra_ids['cost_center_id'] = $this->CostCenter->id;
            } else {
                $extra_ids['cost_center_id'] = $costCenter['CostCenter']['id'];
            }
        }

        if ($departamento) {
            $customerDepartment = $this->CustomerDepartment->find('first', [
                'conditions' => [
                    'TRIM(CustomerDepartment.name)' => trim($departamento),
                    'CustomerDepartment.customer_id' => $customerId,
                    'CustomerDepartment.data_cancel' => '1901-01-01 00:00:00'
                ]
            ]);

            if (empty($customerDepartment)) {
                $this->CustomerDepartment->create();
                $this->CustomerDepartment->save([
                    'name' => trim($departamento),
                    'customer_id' => $customerId,
                    'user_creator_id' => CakeSession::read("Auth.User.id"),
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


    /**
     * Determina a quantidade consolidada baseada no tipo de cobrança do fornecedor
     * 
     * @param array $supplier Dados do fornecedor
     * @param array $orderItems Array de itens do pedido
     * @param string $cpf CPF do beneficiário (opcional)
     * @return int Quantidade consolidada
     */

    /**
     * Conta quantos customer users distintos têm benefícios de um fornecedor específico em um pedido
     * 
     * @param int $supplierId ID do fornecedor
     * @param int $orderId ID do pedido
     * @return int Quantidade de customer users
     */
    private function countCustomerUsersForSupplier($supplierId, $orderId)
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
                'OrderItem.order_id' => $orderId,
                'Supplier.id' => $supplierId,
                'OrderItem.data_cancel' => '1901-01-01 00:00:00'
            ],
            'fields' => ['COUNT(DISTINCT OrderItem.customer_user_id) as count'],
            'group' => false
        ]);

        return $count > 0 ? $count : 1; // Fallback para 1 se não encontrar nenhum
    }

    /**
     * Soma o valor total de itens de um fornecedor específico em um pedido
     * 
     * @param int $supplierId ID do fornecedor
     * @param int $orderId ID do pedido
     * @return float Valor total
     */
    private function getTotalAmountForSupplierInOrder($supplierId, $orderId)
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
                'OrderItem.order_id' => $orderId,
                'Supplier.id' => $supplierId,
                'OrderItem.data_cancel' => '1901-01-01 00:00:00'
            ],
            'fields' => ['SUM(OrderItem.subtotal) as total_amount'],
            'group' => false
        ]);

        $totalAmount = isset($result[0]['total_amount']) ? floatval($result[0]['total_amount']) : 0;
        return $totalAmount > 0 ? $totalAmount : 1; // Fallback para 1 se não encontrar nenhum valor
    }

    /**
     * Calcula e distribui transfer fees para todos os itens de um pedido
     * Este método é chamado sempre que há mudanças no pedido
     * 
     * @param int $orderId ID do pedido
     */
    public function recalculateOrderTransferFees($orderId)
    {
        App::uses('RepaymentCalculator', 'Lib');
        
        // Buscar todos os itens do pedido
        $orderItems = $this->OrderItem->find('all', [
            'contain' => [
                'CustomerUserItinerary' => [
                    'Benefit' => [
                        'Supplier'
                    ]
                ]
            ],
            'conditions' => [
                'OrderItem.order_id' => $orderId,
                'OrderItem.data_cancel' => '1901-01-01 00:00:00'
            ]
        ]);

        if (empty($orderItems)) {
            return;
        }

        // Agrupar itens por fornecedor
        $supplierGroups = [];
        foreach ($orderItems as $item) {
            $supplierId = $item['CustomerUserItinerary']['Benefit']['Supplier']['id'];
            $supplierGroups[$supplierId][] = $item;
        }

        // Calcular fees por fornecedor usando RepaymentCalculator
        foreach ($supplierGroups as $supplierId => $items) {
            $supplier = $items[0]['CustomerUserItinerary']['Benefit']['Supplier'];
            $this->calculateTransferFeesForSupplier($orderId, $supplierId, $supplier, $items);
        }
    }

    /**
     * Parse formatted Brazilian number (1.234,56) to float
     */
    private function parseFormattedNumber($formattedValue)
    {
        // Handle null, empty, or non-string values
        if ($formattedValue === null || $formattedValue === '') {
            return 0.0;
        }
        
        if (is_numeric($formattedValue)) {
            return floatval($formattedValue);
        }
        
        // Handle Brazilian format: 1.234,56 -> 1234.56
        $value = trim(strval($formattedValue));
        
        // If empty after trimming, return 0
        if ($value === '') {
            return 0.0;
        }
        
        // Handle Brazilian number format
        if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
            // Both dot and comma present: dot = thousands separator, comma = decimal
            // Example: 1.234,56 -> 1234.56
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            // Only comma present: decimal separator
            // Example: 123,45 -> 123.45
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, '.') !== false) {
            // Only dot present: check if it's thousands separator or decimal
            // If more than 2 digits after dot, or if ends with .000, it's thousands
            $dotPos = strrpos($value, '.');
            $afterDot = substr($value, $dotPos + 1);
            if (strlen($afterDot) == 3 && ctype_digit($afterDot)) {
                // Example: 1.000 -> 1000 (thousands separator)
                $value = str_replace('.', '', $value);
            }
            // Otherwise keep as decimal: 123.45 -> 123.45
        }
        
        return floatval($value);
    }


    /**
     * Centralized method to calculate transfer fees for a supplier using RepaymentCalculator
     * Implements the tier-based system for all transfer fee types
     */
    private function calculateTransferFeesForSupplier($orderId, $supplierId, $supplier, $items)
    {
        CakeLog::write('debug', "OrdersController: Calculating transfer fees for supplier {$supplierId} using RepaymentCalculator");
        
        // Calculate total subtotal for tier determination
        $totalSubtotal = 0;
        foreach ($items as $item) {
            $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
                ? $item['OrderItem']['subtotal_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
            $totalSubtotal += $itemSubtotal;
        }
        
        try {
            // Get calculation result from RepaymentCalculator using total subtotal to determine tier
            $calculationResult = RepaymentCalculator::calculateRepayment($supplierId, $totalSubtotal, $totalSubtotal);
            
            CakeLog::write('debug', "OrdersController: RepaymentCalculator result: " . json_encode($calculationResult));
            
            $transferFeeType = $supplier['transfer_fee_type'];
            $tipoCobranca = isset($supplier['tipo_cobranca']) ? $supplier['tipo_cobranca'] : 'pedido';
            
            // Apply the calculation based on transfer_fee_type + tipo_cobranca combination
            if ($transferFeeType == 1) { // Fixed Value
                if ($tipoCobranca == 'cpf') {
                    // Fixed by CPF: Apply tier's fixed value to each item individually
                    $this->applyFixedValueByCpf($calculationResult, $items);
                } else {
                    // Fixed by Order: Apply tier's fixed value once, divide among all items
                    $this->applyFixedValueByOrder($calculationResult, $items);
                }
            } elseif ($transferFeeType == 2) { // Percentage
                // Percentage by Order: Apply tier's percentage to each item individually 
                $this->applyPercentageToEachItem($calculationResult, $items);
            } else {
                CakeLog::write('error', "OrdersController: Unsupported transfer_fee_type: {$transferFeeType} for supplier {$supplierId}");
                return;
            }
            
        } catch (Exception $e) {
            CakeLog::write('error', "OrdersController: RepaymentCalculator failed for supplier {$supplierId}: " . $e->getMessage());
            return;
        }
    }
    
    /**
     * Fixed by CPF: Apply tier's fixed value to each item individually
     */
    private function applyFixedValueByCpf($calculationResult, $items)
    {
        $fixedValue = $calculationResult['repayment_value'];
        
        if ($fixedValue <= 0) {
            CakeLog::write('debug', "OrdersController: No fixed value from tier, skipping");
            return;
        }
        
        foreach ($items as $item) {
            $this->updateOrderItemWithTransferFee($item, $fixedValue, [
                'type' => 'fixed_by_cpf',
                'tier_used' => $calculationResult['tier_used'],
                'fixed_value' => $fixedValue,
                'calculation_method' => $calculationResult['calculation_method'],
                'billing_type' => 'cpf'
            ]);
        }
    }
    
    /**
     * Fixed by Order: Apply tier's fixed value to only the first item
     */
    private function applyFixedValueByOrder($calculationResult, $items)
    {
        $totalFixedValue = $calculationResult['repayment_value'];
        
        if ($totalFixedValue <= 0) {
            CakeLog::write('debug', "OrdersController: No fixed value from tier, skipping");
            return;
        }
        
        $itemCount = count($items);
        
        // Apply full transfer fee to only the first item
        $firstItem = reset($items);
        $this->updateOrderItemWithTransferFee($firstItem, $totalFixedValue, [
            'type' => 'volume_tier_fixed',
            'tier_used' => $calculationResult['tier_used'],
            'total_fixed_value' => $totalFixedValue,
            'item_count' => $itemCount,
            'calculation_method' => $calculationResult['calculation_method'],
            'billing_type' => 'pedido'
        ]);
        
        // Set transfer fee to 0 for remaining items
        foreach (array_slice($items, 1) as $item) {
            $this->updateOrderItemWithTransferFee($item, 0, [
                'type' => 'volume_tier_fixed',
                'tier_used' => $calculationResult['tier_used'],
                'total_fixed_value' => 0,
                'item_count' => $itemCount,
                'calculation_method' => $calculationResult['calculation_method'],
                'billing_type' => 'pedido'
            ]);
        }
    }
    
    /**
     * Percentage by Order: Apply tier's percentage to each item individually
     */
    private function applyPercentageToEachItem($calculationResult, $items)
    {
        $percentage = $calculationResult['repayment_percentage'];
        
        if ($percentage <= 0) {
            CakeLog::write('debug', "OrdersController: No percentage from tier, skipping");
            return;
        }
        
        foreach ($items as $item) {
            $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
                ? $item['OrderItem']['subtotal_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
                
            $itemTransferFee = ($itemSubtotal * $percentage) / 100;
            
            $this->updateOrderItemWithTransferFee($item, $itemTransferFee, [
                'type' => 'percentage_individual',
                'tier_used' => $calculationResult['tier_used'],
                'percentage' => $percentage,
                'item_subtotal' => $itemSubtotal,
                'calculated_fee' => $itemTransferFee,
                'calculation_method' => $calculationResult['calculation_method'],
                'billing_type' => 'pedido'
            ]);
        }
    }
    
    /**
     * Update order item with calculated transfer fee
     */
    private function updateOrderItemWithTransferFee($item, $transferFee, $calculationDetails)
    {
        // Parse and validate commission fee
        $itemCommissionFee = isset($item['OrderItem']['commission_fee_not_formated']) 
            ? $item['OrderItem']['commission_fee_not_formated'] 
            : (isset($item['OrderItem']['commission_fee']) ? $item['OrderItem']['commission_fee'] : 0);
        $itemCommissionFee = $this->parseFormattedNumber($itemCommissionFee);
        
        // Parse and validate subtotal
        $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
            ? $item['OrderItem']['subtotal_not_formated'] 
            : (isset($item['OrderItem']['subtotal']) ? $item['OrderItem']['subtotal'] : 0);
        $itemSubtotal = $this->parseFormattedNumber($itemSubtotal);
        
        // Ensure transfer fee is numeric
        $transferFee = $this->parseFormattedNumber($transferFee);
        
        CakeLog::write('debug', "OrdersController: Calculating total - Subtotal: {$itemSubtotal}, Transfer Fee: {$transferFee}, Commission: {$itemCommissionFee}");
        
        $newTotal = $itemSubtotal + $transferFee + $itemCommissionFee;
        
        $calculationLog = json_encode($calculationDetails);
        
        // Update the order item
        $updateData = [
            'OrderItem' => [
                'id' => $item['OrderItem']['id'],
                'transfer_fee' => $transferFee,
                'total' => $newTotal,
                'calculation_details_log' => $calculationLog
            ]
        ];
        
        if ($this->OrderItem->save($updateData, ['callbacks' => false, 'validate' => false])) {
            CakeLog::write('debug', "OrdersController: Successfully updated OrderItem {$item['OrderItem']['id']} with transfer_fee: {$transferFee}");
        } else {
            CakeLog::write('error', "OrdersController: Failed to update OrderItem {$item['OrderItem']['id']}. Errors: " . json_encode($this->OrderItem->validationErrors));
        }
    }

    public function nota_debito_unificada()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;
        
        $ids = $this->request->query('ids');
        $valor_informado = $this->request->query('valor');
        $motivo = $this->request->query('motivo');

        if ($valor_informado) {
            $valor_informado = $this->priceFormatBeforeSave($valor_informado);
        } else {
            $valor_informado = 0;
        }

        $orders = $this->Order->find('all', [
            'contain' => ['Customer', 'EconomicGroup'],
            'conditions' => ['Order.id' => $ids],
        ]);
        
        $baseOrder = $orders[0];
        
        $itensConsolidados = [];
        $totalSubtotal = 0;
        $totalTransferFee = 0;
        $totalDesconto = 0;
        $listaPedidos = [];
        
        foreach ($orders as $order) {
            $listaPedidos[] = $order['Order']['id'];
            
            $totalSubtotal += $order['Order']['subtotal_not_formated'];
            $totalTransferFee += $order['Order']['transfer_fee_not_formated'];
            $totalDesconto += $order['Order']['desconto_not_formated'];
            
            $itens = $this->OrderItem->find('all', [
                'fields' => [
                    'CustomerUserItinerary.benefit_id',
                    'Benefit.name',
                    'count(CustomerUserItinerary.quantity) as qtd',
                    'round(sum(OrderItem.subtotal),2) as valor',
                ],
                'joins' => [
                    [
                        'table' => 'benefits',
                        'alias' => 'Benefit',
                        'type' => 'INNER',
                        'conditions' => ['Benefit.id = CustomerUserItinerary.benefit_id']
                    ]
                ],
                'conditions' => ['OrderItem.order_id' => $order['Order']['id']],
                'group' => ['CustomerUserItinerary.benefit_id']
            ]);
            
            foreach ($itens as $item) {
                $benefitId = $item['CustomerUserItinerary']['benefit_id'];
                
                if (!isset($itensConsolidados[$benefitId])) {
                    $itensConsolidados[$benefitId] = [
                        'benefit_name' => $item['Benefit']['name'],
                        'qtd' => 0,
                        'valor' => 0
                    ];
                }
                
                $itensConsolidados[$benefitId]['qtd'] += $item[0]['qtd'];
                $itensConsolidados[$benefitId]['valor'] += $item[0]['valor'];
            }
        }
        
        $itensFormatados = [];
        foreach ($itensConsolidados as $benefitId => $item) {
            $itensFormatados[] = [
                'CustomerUserItinerary' => [
                    'benefit_id' => $benefitId,
                    'benefit_name' => $item['benefit_name']
                ],
                0 => [
                    'qtd' => $item['qtd'],
                    'valor' => $item['valor']
                ]
            ];
        }
        
        $numeroNotaUnificada = implode(', ', $listaPedidos);
        
        $orderConsolidado = [
            'Order' => [
                'id' => $numeroNotaUnificada,
                'motivo' => $motivo,
                'valor_informado' => number_format($valor_informado, 2, ',', '.'),
                'subtotal' => number_format($totalSubtotal, 2, ',', '.'),
                'subtotal_not_formated' => $totalSubtotal,
                'transfer_fee' => number_format($totalTransferFee, 2, ',', '.'),
                'transfer_fee_not_formated' => $totalTransferFee,
                'desconto' => number_format($totalDesconto, 2, ',', '.'),
                'desconto_not_formated' => $totalDesconto,
                'total' => number_format(
                    ($totalSubtotal + $totalTransferFee - $valor_informado), 
                    2, ',', '.'
                ),
                'economic_group_id' => $baseOrder['Order']['economic_group_id']
            ],
            'Customer' => $baseOrder['Customer'],
            'EconomicGroup' => $baseOrder['EconomicGroup']
        ];
        
        $allOrders = [
            [
                'order' => $orderConsolidado,
                'itens' => $itensFormatados,
                'lista_pedidos' => $listaPedidos
            ]
        ];
        
        $view = new View($this, false);
        $view->layout = false;
        $link = APP . 'webroot';
        
        $view->set(compact("link", "allOrders"));
        $html = $view->render('../Elements/nota_debito_unificada');
        
        $nomeArquivo = 'nota_debito_unificada_' . date('Ymd_His') . '.pdf';
        $this->HtmltoPdf->convert($html, $nomeArquivo, 'download');
    }

    public function descontos($id) 
    {
        ini_set('pcre.backtrack_limit', '15000000');
        ini_set('memory_limit', '-1');
        
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");

        if (!$id) {
            $this->Flash->error('Pedido não encontrado');
            $this->redirect(['action' => 'index']);
            return;
        }

        $order = $this->Order->findById($id);

        if (!$order) {
            $this->Flash->error('Pedido não encontrado');
            $this->redirect(['action' => 'index']);
            return;
        }

        $this->paginate = [
            'OrderDiscountBatch' => [
                'contain' => [
                    'UserCreator'
                ],
                'order' => 'OrderDiscountBatch.created DESC',
                'limit' => 10
            ]
        ];
        
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['OrderDiscountBatch.order_id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                                                            'OrderDiscountBatch.id LIKE' => "%" . $_GET['q'] . "%", 
                                                            'OrderDiscountBatch.discount_type LIKE' => "%" . $_GET['q'] . "%",
                                                            'OrderDiscountBatch.observacao LIKE' => "%" . $_GET['q'] . "%"
                                                            ]);
        }

        $batches = $this->Paginator->paginate('OrderDiscountBatch', $condition);

        $available_orders = $this->Order->find('all', [
            'fields' => [
                'Order.id',
                'Customer.nome_primario'
            ],
            'joins' => [
                [
                    'table' => 'order_discount_batch_items',
                    'alias' => 'OrderDiscountBatchItem',
                    'type' => 'LEFT',
                    'conditions' => [
                        'OrderDiscountBatchItem.order_parent_id = Order.id'
                    ]
                ],
                [
                    'table' => 'order_discount_batches',
                    'alias' => 'OrderDiscountBatch',
                    'type' => 'LEFT',
                    'conditions' => [
                        'OrderDiscountBatch.id = OrderDiscountBatchItem.batch_id',
                        'OrderDiscountBatch.data_cancel' => '1901-01-01 00:00:00'
                    ]
                ]
            ],
            'conditions' => [
                'Order.id !=' => $id,
                'Order.customer_id' => $order['Order']['customer_id'],
                'OrderDiscountBatch.id IS NULL'
            ],
            'order' => [
                'Order.id' => 'DESC'
            ],
            'group' => 'Order.id'
        ]);

        $orders = [];
        foreach ($available_orders as $order) {
            $orders[$order['Order']['id']] = $order['Order']['id'].' - '.$order['Customer']['nome_primario'];
        }

        $action = 'Descontos';
        $breadcrumb = [
            'Cadastros' => ['controller' => 'orders', 'action' => 'edit', $id],
            'Descontos' => '',
        ];

        $this->set(compact('id', 'order', 'batches', 'breadcrumb', 'action', 'orders'));
    }

    public function criar_lote_desconto() 
    {
        $this->autoRender = false;
        
        if (!$this->request->is('post')) {
            echo json_encode([
                'success' => false,
                'message' => 'Método não permitido'
            ]);
            return;
        }

        $order_id       = $this->request->data['order_id'];
        $pedidos        = $this->request->data['pedidos'];
        $discount_type  = $this->request->data['discount_type'];
        $observacao     = isset($this->request->data['observacao']) ? $this->request->data['observacao'] : '';
        $valor_total    = $this->request->data['valor_total'];        
        
        $qtdPedidos = count($pedidos);
        
        $data = [
            'order_id' => $order_id,
            'discount_type' => $discount_type,
            'observacao' => $observacao,
            'valor_total' => $valor_total,
            'quantidade_pedidos' => $qtdPedidos,
            'user_creator_id' => $this->Auth->user('id'),
            'created' => date('Y-m-d H:i:s')
        ];

        $this->OrderDiscountBatch->create();
        $this->OrderDiscountBatch->save($data);

        $batchId = $this->OrderDiscountBatch->id;

        foreach ($pedidos as $orderParentId) {
            $item = [
                'batch_id' => $batchId,
                'order_parent_id' => $orderParentId,
                'created' => date('Y-m-d H:i:s')
            ];

            $this->OrderDiscountBatchItem->create();
            $this->OrderDiscountBatchItem->save($item);
        }

        $order_discount = $this->OrderDiscountBatch->find('first', [
            'fields' => ['SUM(OrderDiscountBatch.valor_total) as total'],
            'conditions' => [
                'OrderDiscountBatch.order_id' => $order_id,
                'OrderDiscountBatch.data_cancel' => '1901-01-01 00:00:00'
            ]
        ]);

        $desconto_total = $order_discount[0]['total'];
        
        $this->Order->save([
            'Order' => [
                'id' => $order_id,
                'desconto' => $desconto_total,
                'user_updated_id' => CakeSession::read("Auth.User.id"),
                'updated' => date('Y-m-d H:i:s'),
            ]
        ]);

        echo json_encode(['success' => true]);
    }

    public function lote_desconto($id, $batch_id)
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $items = $this->OrderDiscountBatchItem->find('all', [
            'conditions' => ['OrderDiscountBatch.order_id' => $id, 'OrderDiscountBatchItem.batch_id' => $batch_id],            
            'fields' => [
                'OrderDiscountBatchItem.*',
                'Order.id',
                'Order.total',
                'Customer.documento',
                'Customer.nome_secundario',
            ],
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => ['Customer.id = Order.customer_id'],
                ],
            ],
        ]);

        $action = 'Descontos';
        $breadcrumb = [
            'Cadastros' => ['controller' => 'orders', 'action' => 'edit', $id],
            'Descontos' => '',
            'Editar Desconto' => '',
        ];

        $this->set(compact('id', 'batch_id', 'breadcrumb', 'action', 'items'));
    }

    public function delete_lote_desconto($id, $batch_id)
    {
        $this->Permission->check(63, "excluir") ? "" : $this->redirect("/not_allowed");

		$this->OrderDiscountBatch->id = $batch_id;

		$data = ['OrderDiscountBatch' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->OrderDiscountBatch->save($data)) {
            $order_discount = $this->OrderDiscountBatch->find('first', [
                'fields' => ['SUM(OrderDiscountBatch.valor_total) as total'],
                'conditions' => [
                    'OrderDiscountBatch.order_id' => $id,
                    'OrderDiscountBatch.data_cancel' => '1901-01-01 00:00:00'
                ]
            ]);

            $desconto_total = $order_discount[0]['total'];
            
            $this->Order->save([
                'Order' => [
                    'id' => $id,
                    'desconto' => $desconto_total,
                    'user_updated_id' => CakeSession::read("Auth.User.id"),
                    'updated' => date('Y-m-d H:i:s'),
                ]
            ]);

            $this->Flash->set(__('O Desconto foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'descontos/' . $id]);
        }
    }
}
