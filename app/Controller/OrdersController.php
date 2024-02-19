<?php
App::uses('ApiItau', 'Lib');

use League\Csv\Reader;

class OrdersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'HtmltoPdf'];
    public $uses = ['Order', 'Customer', 'CustomerUserItinerary', 'Benefit', 'OrderItem', 'CustomerUserVacation', 'CustomerUser', 'Income', 'Bank', 'BankTicket', 'CnabLote', 'CnabItem', 'PaymentImportLog', 'EconomicGroup', 'BenefitType', 'Outcome'];
    public $groupBenefitType = [
        -1 => [1,2],
        4 => [4,5],
        999 => [6,7,8,9,10]
    ];

    public $paginate = [
        'Order' => [
            'limit' => 10, 'order' => ['Order.id' => 'desc']
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Order.id' => $_GET['q'], 'Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%", 'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $data = $this->Paginator->paginate('Order', $condition);
        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'order' => ['nome_primario' => 'asc']]);

        $benefit_types = [-1 => 'Transporte', 4 => 'PAT', 999 => 'Outros'];

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'customers', 'benefit_types'));
    }

    public function createOrder()
    {
        $this->autoRender = false;
        $customerId = $this->request->data['customer_id'];
        $workingDays = $this->request->data['working_days'];
        $period_from = $this->request->data['period_from'];
        $period_to = $this->request->data['period_to'];
        $is_consolidated = $this->request->data['is_consolidated'];
        $is_partial = $this->request->data['is_partial'];
        $working_days_type = $this->request->data['working_days_type'];
        $grupo_especifico = $this->request->data['grupo_especifico'];
        $benefit_type = $this->request->data['benefit_type'];
        $credit_release_date = $this->request->data['credit_release_date'] ? $this->request->data['credit_release_date'] : date('d/m/Y', strtotime(' + 5 day'));


        if ($this->request->is('post')) {
            if ($is_consolidated == 2) {
                $orderId = $this->processConsolidated($customerId, $workingDays, $period_from, $period_to, $is_partial, $credit_release_date, $working_days_type, $grupo_especifico, $benefit_type);
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
                    'CustomerUser.id is not null',
                    'CustomerUser.status_id' => 1,
                    'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
                ];

                $benefit_type_persist = 0;
                if ($benefit_type != '') {
                    $benefit_type_persist = $benefit_type;
                    $benefit_type = (int)$benefit_type;
                    if($benefit_type == -1){
                        $benefit_type = [1,2];
                    }
                    $condNotPartial['Benefit.benefit_type_id'] = $benefit_type;
                }

                $customerItineraries = $this->CustomerUserItinerary->find('all', [
                    'conditions' => $condNotPartial,
                    'recursive' => 2
                ]);

                if (empty($customerItineraries)) {
                    $this->Flash->set(__('Nenhum itinerário encontrado para este cliente.'), ['params' => ['class' => "alert alert-danger"]]);
                    $this->redirect(['action' => 'index']);
                }
            }


            $orderData = [
                'customer_id' => $customerId,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period_from' => $period_from,
                'order_period_to' => $period_to,
                'status_id' => 83,
                'credit_release_date' => $credit_release_date,
                'created_at' => date('Y-m-d H:i:s'),
                'working_days_type' => $working_days_type,
                'benefit_type' => $benefit_type_persist,
            ];

            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();

                if ($is_partial == 2) {
                    $this->processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type);
                }

                $this->Order->id = $orderId;
                $this->Order->reProcessAmounts($orderId);

                $this->Flash->set(__('Pedido gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('Falha ao criar pedido. Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }

            $this->redirect(['action' => 'edit/' . $orderId]);
        }
    }

    public function processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type, $manualPricing = [])
    {
        $totalTransferFee = 0;
        $totalSubtotal = 0;
        $totalOrder = 0;

        foreach ($customerItineraries as $itinerary) {
            if (!empty($manualPricing)) {
                $manualUnitPrice = $manualPricing[$itinerary['CustomerUserItinerary']['customer_user_id']];
                $itinerary['CustomerUserItinerary']['price_per_day_not_formated'] = $manualUnitPrice * $itinerary['CustomerUserItinerary']['quantity'];
            }
            
            $pricePerDay = $itinerary['CustomerUserItinerary']['price_per_day_not_formated'];
            $vacationDays = $this->CustomerUserVacation->getVacationsDays($itinerary['CustomerUserItinerary']['customer_user_id'], $period_from, $period_to);

            if ($working_days_type == 2) {
                $workingDays = $itinerary['CustomerUserItinerary']['working_days'];
            }

            $workingDaysUser = $workingDays - $vacationDays;

            if ($workingDaysUser < 0) {
                $workingDaysUser = 0;
            }

            $subtotal = $workingDaysUser * $pricePerDay;

            $benefitId = $itinerary['CustomerUserItinerary']['benefit_id'];
            $benefit = $this->Benefit->findById($benefitId);
            $transferFeePercentage = isset($benefit['Supplier']['transfer_fee_percentage_nao_formatado'])
                ? $benefit['Supplier']['transfer_fee_percentage_nao_formatado']
                : 0;
            $transferFee = $subtotal * ($transferFeePercentage / 100);

            $total = $subtotal + $transferFee;

            $totalTransferFee += $transferFee;
            $totalSubtotal += $subtotal;
            $totalOrder += $total;

            $orderItemData = [
                'order_id' => $orderId,
                'customer_user_itinerary_id' => $itinerary['CustomerUserItinerary']['id'],
                'customer_user_id' => $itinerary['CustomerUserItinerary']['customer_user_id'],
                'working_days' => $workingDaysUser,
                'price_per_day' => $pricePerDay,
                'subtotal' => $subtotal,
                'transfer_fee' => $transferFee,
                'total' => $total,
            ];

            $this->OrderItem->create();
            $this->OrderItem->save($orderItemData);
        }
    }

    public function edit($id = null)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Order->id = $id;
        $old_order = $this->Order->read();

        if ($this->request->is(['post', 'put'])) {
            if ($old_order['Order']['status_id'] < 85) {
                if ($old_order['Order']['desconto'] > 0 && $this->request->data['Order']['desconto'] == '') {
                    $total = ($old_order['Order']['transfer_fee_not_formated'] + $old_order['Order']['commission_fee_not_formated'] + $old_order['Order']['subtotal_not_formated']) + isset($old_order['Order']['desconto_not_formated']);
                } else {
                    $total = ($old_order['Order']['transfer_fee_not_formated'] + $old_order['Order']['commission_fee_not_formated'] + $old_order['Order']['subtotal_not_formated']) - $this->priceFormatBeforeSave($this->request->data['Order']['desconto']);
                }
                $order = ['Order' => []];
                $order['Order']['id'] = $id;
                $order['Order']['desconto'] = $this->request->data['Order']['desconto'];
                $order['Order']['total'] = $total;
                $order['Order']['observation'] = $this->request->data['Order']['observation'];
                $order['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");
            }

            if (($old_order['Order']['status_id'] == 86 || $old_order['Order']['status_id'] == 85) && !empty($this->request->data['Order']['end_date'])) {
                $order['Order']['id'] = $id;
                $order['Order']['status_id'] = 87;
                $order['Order']['end_date'] = $this->request->data['Order']['end_date'];
            }

            if ($this->Order->save($order)) {
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
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $_GET['q'] . "%"]);
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

        $economic_group = null;
        if ($order['Order']['economic_group_id'] != null) {
            $economic_group = $this->EconomicGroup->findById($order['Order']['economic_group_id']);
        }

        $benefit_type_desc = 'Todos';
        if ($order['Order']['benefit_type'] != 0) {
            if($order['Order']['benefit_type'] == -1){
                $benefit_type_desc = 'Transporte';
            } else {
                $benefit_types = $this->BenefitType->find('first', [
                    'conditions' => ['BenefitType.id' => $order['Order']['benefit_type']]
                ]);
                
                $benefit_type_desc = $benefit_types['BenefitType']['name'];
            }
        }

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => '', 'Alterar Pedido' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items', 'progress'));
        $this->set(compact('suppliersCount', 'usersCount', 'income', 'benefits', 'gerarNota', 'economic_group', 'benefit_type_desc'));

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

        $this->Order->recursive = -1;
        $order = $this->Order->findById($id);

        $bankTicket = $this->BankTicket->find('first', [
            'conditions' => ['BankTicket.status_id' => 1]
        ]);

        $income = [];

        $income['Income']['order_id'] = $id;
        $income['Income']['parcela'] = 1;
        $income['Income']['status_id'] = 15;
        $income['Income']['bank_account_id'] = $bankTicket['Bank']['id'];
        $income['Income']['customer_id'] = $order['Order']['customer_id'];
        $income['Income']['name'] = 'Conta a receber - Pedido ' . $order['Order']['id'];
        $income['Income']['valor_multa'] = 0;
        $income['Income']['valor_bruto'] = $order['Order']['total'];
        $income['Income']['valor_total'] = $order['Order']['total'];
        $income['Income']['vencimento'] = date('d/m/Y', strtotime(' + 30 day'));;
        $income['Income']['data_competencia'] = date('01/m/Y');
        $income['Income']['created'] = date('Y-m-d H:i:s');
        $income['Income']['user_creator_id'] = CakeSession::read("Auth.User.id");

        $this->Income->create();
        $this->Income->save($income);

        if ($this->emitirBoleto($this->Income->id)) {
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
                        'Customer.id = Income.customer_id', 'Customer.data_cancel' => '1901-01-01',
                    ],
                ],
                [
                    'table' => 'bank_accounts',
                    'alias' => 'BankAccount',
                    'type' => 'inner',
                    'conditions' => [
                        'BankAccount.id = Income.bank_account_id', 'BankAccount.data_cancel' => '1901-01-01',
                    ],
                ],
                [
                    'table' => 'bank_tickets',
                    'alias' => 'BankTicket',
                    'type' => 'inner',
                    'conditions' => [
                        'BankAccount.id = BankTicket.bank_account_id', 'BankTicket.data_cancel' => '1901-01-01',
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

            $ApiItau = new ApiItau();

            $boleto = $ApiItau->gerarBoleto($conta);

            if ($boleto['success']) {
                $this->CnabItem->create();
                $this->CnabItem->save([
                    'CnabItem' => [
                        'cnab_lote_id' => $this->CnabLote->id,
                        'income_id' => $conta['Income']['id'],
                        'id_web' => $boleto['contents']['data']['dado_boleto']['dados_individuais_boleto'][0]['numero_nosso_numero'],
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
        $cond = ['CustomerUserItinerary.customer_user_id' => $customerUserId];

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
        ]);

        $this->processItineraries($customerItineraries, $orderId, $workingDays, $order['Order']['order_period_from'], $order['Order']['order_period_to'], 1);

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);

        $this->Flash->set(__('Beneficiário incluído com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $orderId]);
    }

    public function upload_user_csv()
    {
        $orderId = $this->request->data['order_id'];
        $customerId = $this->request->data['customer_id'];

        $file = $this->request->data['CustomerUserItinerary'];
        $incluir_valor_unitario = (int)$this->request->data['incluir_valor_unitario'] == 1;

        $ret = $this->parseCSVwithCPFColumn($customerId, $file['file']['tmp_name'], $incluir_valor_unitario);
        $customerUsersIds = $ret['customerUsersIds'];
        $manualPricing = $ret['unitPriceMaping'];

        $order = $this->Order->findById($orderId);
        $cond = ['CustomerUserItinerary.customer_user_id' => $customerUsersIds];

        if ($order['Order']['benefit_type'] != 0) {
            $benefit_type = $order['Order']['benefit_type'];
            $benefit_type = $this->groupBenefitType[$benefit_type];
            $cond['Benefit.benefit_type_id'] = $benefit_type;
        }

        $customerItineraries = $this->CustomerUserItinerary->find('all', [
            'conditions' => $cond,
        ]);

        $this->processItineraries($customerItineraries, $orderId, $order['Order']['working_days'], $order['Order']['order_period_from'], $order['Order']['order_period_to'], $order['Order']['working_days_type'], $manualPricing);

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);

        $this->Flash->set(__('Beneficiários incluído com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $orderId]);
    }

    private function parseCSVwithCPFColumn($customerId, $tmpFile, $include_new_price = false)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 2) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $line = 0;
        $customerUsersIds = [];
        $unitPrice = 0;
        $unitPriceMaping = [];
        foreach ($csv->getRecords() as $row) {
            $unitPrice = 0;
            if ($line == 0 || empty($row[0])) {
                if ($line == 0) {
                    $line++;
                }
                continue;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);            

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

            if($include_new_price){
                $unitPrice = $row[1];
                // convert brl string to float
                $unitPrice = str_replace(".", "", $unitPrice);
                $unitPrice = (float)str_replace(",", ".", $unitPrice);
                $unitPriceMaping[$existingUser['CustomerUser']['id']] = $unitPrice;
            }

            $customerUsersIds[] = $existingUser['CustomerUser']['id'];

            $line++;
        }

        return ['customerUsersIds' => $customerUsersIds, 'unitPriceMaping' => $unitPriceMaping];
    }

    public function updateWorkingDays()
    {
        $this->autoRender = false;

        $itemId = $this->request->data['orderItemId'];

        $orderItem = $this->OrderItem->findById($itemId);

        if ($this->request->data['campo'] == 'working_days') {
            $workingDays = $this->request->data['newValue'];
            $orderItem['OrderItem']['working_days'] = $workingDays;
            $var = $orderItem['OrderItem']['var_not_formated'];
        } else {
            $workingDays = $orderItem['OrderItem']['working_days'];
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
        $transferFee = $orderItem['OrderItem']['subtotal'] * ($transferFeePercentage / 100);

        $orderItem['OrderItem']['transfer_fee'] = $transferFee;

        $orderItem['OrderItem']['total'] = $orderItem['OrderItem']['subtotal'] + $transferFee;

        $this->OrderItem->id = $itemId;
        $this->OrderItem->save($orderItem);

        $orderItem = $this->OrderItem->findById($itemId);

        $this->Order->id = $orderItem['OrderItem']['order_id'];
        $this->Order->reProcessAmounts($orderItem['OrderItem']['order_id']);

        $order = $this->Order->findById($orderItem['OrderItem']['order_id']);

        $pedido_subtotal = $order['Order']['subtotal'];
        $pedido_transfer_fee = $order['Order']['transfer_fee'];
        $pedido_total = $order['Order']['total'];

        echo json_encode([
            'success' => true,
            'subtotal' => $orderItem['OrderItem']['subtotal'],
            'transfer_fee' => $orderItem['OrderItem']['transfer_fee'],
            'total' => $orderItem['OrderItem']['total'],
            'pedido_subtotal' => $pedido_subtotal,
            'pedido_transfer_fee' => $pedido_transfer_fee,
            'pedido_total' => $pedido_total
        ]);
    }

    public function removeOrderItem($orderId = false, $itemOrderId = false)
    {
        $this->autoRender = false;

        $is_multiple = false;
        if($orderId == false || $itemOrderId == false){
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

        if($is_multiple){
            echo json_encode(['success' => true]);
        } else {
            $this->redirect('/orders/edit/' . $orderId);
        }

        
    }

    public function addItinerary()
    {
        $id = $this->request->data['customer_id'];
        $orderId = $this->request->data['order_id'];

        $this->CustomerUserItinerary->create();
        $this->CustomerUserItinerary->validates();

        $this->request->data['CustomerUserItinerary']['user_creator_id'] = CakeSession::read("Auth.User.id");
        $this->request->data['CustomerUserItinerary']['customer_id'] = $id;

        if ($this->CustomerUserItinerary->save($this->request->data)) {

            $idLastInserted = $this->CustomerUserItinerary->getLastInsertId();

            $order = $this->Order->findById($orderId);

            $customerItineraries = $this->CustomerUserItinerary->find('all', [
                'conditions' => ['CustomerUserItinerary.id' => $idLastInserted],
                'recursive' => 2
            ]);

            $this->processItineraries($customerItineraries, $orderId, $order['Order']['working_days'], $order['Order']['order_period_from'], $order['Order']['order_period_to'], 1);

            $this->Order->id = $orderId;
            $this->Order->reProcessAmounts($orderId);

            $this->Flash->set(__('Itinerário adicionado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect('/orders/edit/' . $orderId);
        } else {
            $this->Flash->set(__('Itinerário não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
        }
    }

    private function processConsolidated($customerId, $workingDays, $period_from, $period_to, $is_partial, $credit_release_date, $working_days_type, $grupo_especifico, $benefit_type)
    {
        $cond = [
            'CustomerUserItinerary.customer_id' => $customerId,
            'CustomerUser.status_id' => 1,
            'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
        ];
        if ($grupo_especifico != '') {
            $cond['CustomerUserEconomicGroup.economic_group_id'] = $grupo_especifico;
        }
        if ($benefit_type != '') {
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
                'CustomerUser.status_id' => 1,
                'CustomerUser.data_cancel' => '1901-01-01 00:00:00',
                'CustomerUser.id' => $user_list
            ];

            $benefit_type_persist = 0;
            if ($benefit_type != '') {
                $benefit_type = (int)$benefit_type;
                $benefit_type_persist = (int)$benefit_type;
                $benefit_type = $this->groupBenefitType[$benefit_type];
                $cond2['Benefit.benefit_type_id'] = $benefit_type;
            }

            if ($is_partial == 2) {
                $customerItineraries = $this->CustomerUserItinerary->find('all', [
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
                            'table' => 'benefits',
                            'alias' => 'Benefit',
                            'type' => 'INNER',
                            'conditions' => [
                                'Benefit.id = CustomerUserItinerary.benefit_id'
                            ]
                        ]
                    ],
                    'conditions' => $cond2,
                    'recursive' => -1
                ]);

                if (empty($customerItineraries)) {
                    $this->Flash->set(__('Nenhum itinerário encontrado para este cliente.'), ['params' => ['class' => "alert alert-danger"]]);
                    $this->redirect(['action' => 'index']);
                }
            }

            if ($k == 'NOK') {
                $k = null;
            }

            $orderData = [
                'customer_id' => $customerId,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period_from' => $period_from,
                'order_period_to' => $period_to,
                'status_id' => 83,
                'credit_release_date' => $credit_release_date,
                'created' => date('Y-m-d H:i:s'),
                'economic_group_id' => $k,
                'working_days_type' => $working_days_type,
                'benefit_type' => $benefit_type_persist,
            ];

            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();

                if ($is_partial == 2) {
                    $this->processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to, $working_days_type);
                }

                $this->Order->id = $orderId;
                $this->Order->reProcessAmounts($orderId);

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

    public function Operadoras($id)
    {
        $this->Permission->check(63, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $suppliersAll = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id],
            'fields' => ['Supplier.razao_social', 'sum(OrderItem.subtotal) as subtotal'],
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

        //debug( $suppliersAll);die;
        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Operadores' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'id' ,'suppliersAll'));
    }

    public function confirma_pagamento($id){
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

   
    public function gerar_pagamento($id)
    {
        $this->Permission->check(63, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->autoRender = false;
    
        $suppliersAll = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id],
            'fields' => ['Supplier.id', 'round(sum(OrderItem.subtotal),2) as subtotal'],
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
        //debug($suppliersAll);die;

        foreach ($suppliersAll as $supplier) { 
            $outcome = [];

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
            $outcome['Outcome']['valor_bruto'] =  number_format($supplier[0]['subtotal'],2,',','.');
            $outcome['Outcome']['valor_total'] =  number_format($supplier[0]['subtotal'],2,',','.');
            $outcome['Outcome']['vencimento'] = date('d/m/Y', strtotime(' + 3 day'));;
            $outcome['Outcome']['data_competencia'] = date('01/m/Y');
            $outcome['Outcome']['created'] = date('Y-m-d H:i:s');
            $outcome['Outcome']['user_creator_id'] = CakeSession::read("Auth.User.id");
            // debug($outcome);die;
            $this->Outcome->create();
            $this->Outcome->save($outcome);
        }
        $this->Flash->set(__('Pagamento gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);

        // Redireciona para a página de operadoras
        $this->redirect(['action' => 'operadoras/' . $id]);
        $this->set(compact('id'));
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
                'sum(OrderItem.subtotal) as valor',
                'sum(OrderItem.total) as total',
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


    public function priceFormatBeforeSave($price)
    {
        if (is_numeric($price)) {
            return $price;
        }
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }
}
