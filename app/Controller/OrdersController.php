<?php
class OrdersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Order', 'Customer', 'CustomerUserItinerary', 'Benefit', 'OrderItem', 'CustomerUserVacation', 'CustomerUser', 'Income', 'Bank', 'BankTicket'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Order.name' => 'asc']
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
            $condition['or'] = array_merge($condition['or'], ['Order.name LIKE' => "%" . $_GET['q'] . "%", 'Supplier.nome_fantasia LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $data = $this->Paginator->paginate('Order', $condition);
        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario']]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'customers'));
    }

    public function createOrder()
    {
        $this->autoRender = false;
        $customerId = $this->request->data['customer_id'];
        $workingDays = $this->request->data['working_days'];
        $period_from = $this->request->data['period_from'];
        $period_to = $this->request->data['period_to'];
        $credit_release_date = $this->request->data['credit_release_date'] ? $this->request->data['credit_release_date'] : date('d/m/Y', strtotime(' + 5 day'));

        if ($this->request->is('post')) {
            $customerItineraries = $this->CustomerUserItinerary->find('all', [
                'conditions' => ['CustomerUserItinerary.customer_id' => $customerId],
                'recursive' => 2
            ]);

            $orderData = [
                'customer_id' => $customerId,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period_from' => $period_from,
                'order_period_to' => $period_to,
                'status_id' => 83,
                'credit_release_date' => $credit_release_date
            ];

            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();

                $this->processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to);

                $this->Order->id = $orderId;
                $this->Order->reProcessAmounts($orderId);

                $this->Flash->set(__('Pedido gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('Falha ao criar pedido. Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }

            $this->redirect(['action' => 'edit/' . $orderId]);
        }
    }

    public function processItineraries($customerItineraries, $orderId, $workingDays, $period_from, $period_to)
    {
        $totalTransferFee = 0;
        $totalSubtotal = 0;
        $totalOrder = 0;

        foreach ($customerItineraries as $itinerary) {
            $pricePerDay = $itinerary['CustomerUserItinerary']['price_per_day_not_formated'];
            $workingDaysUser = $this->CustomerUserVacation->calculateWorkingDays($itinerary['CustomerUserItinerary']['customer_user_id'], $period_from, $period_to);

            if($workingDaysUser == null){
                $workingDaysUser = 0;
            }

            if ($workingDaysUser > $workingDays) {
                $workingDaysUser = $workingDays;
            }

            $subtotal = $workingDaysUser * $pricePerDay;

            $benefitId = $itinerary['CustomerUserItinerary']['benefit_id'];
            $benefit = $this->Benefit->findById($benefitId);
            $transferFeePercentage = $benefit['Supplier']['transfer_fee_percentage'];
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
                $order = ['Order' => []];
                $order['Order']['id'] = $id;
                $order['Order']['observation'] = $this->request->data['Order']['observation'];
                $order['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");
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

        $items = $this->Paginator->paginate('OrderItem', ['and' => ['Order.id' => $id]]);

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

        $conditions = [
            'CustomerUser.customer_id' => $order['Order']['customer_id']
        ];

        $order_customer_users = $this->OrderItem->find('all', [
            'conditions' => ['OrderItem.order_id' => $id],
            'group' => ['OrderItem.customer_user_id'],
            'fields' => ['OrderItem.customer_user_id']
        ]);

        $arr_cst_in_order = Hash::extract($order_customer_users, '{n}.OrderItem.customer_user_id');

        $second_condition = [
            'CustomerUserItinerary.customer_id' => $order['Order']['customer_id']
        ];
        if (!empty($arr_cst_in_order)) {
            $conditions['CustomerUser.id NOT IN'] = $arr_cst_in_order;
            $second_condition['CustomerUserItinerary.customer_user_id NOT IN'] = $arr_cst_in_order;
        }

        $users_with_itinerary = $this->CustomerUserItinerary->find('all', [
            'conditions' => $second_condition,
            'group' => ['CustomerUserItinerary.customer_user_id'],
            'fields' => ['CustomerUserItinerary.customer_user_id']
        ]);

        $arr_ust_with_itinerary = Hash::extract($users_with_itinerary, '{n}.CustomerUserItinerary.customer_user_id');

        if (!empty($arr_ust_with_itinerary)) {
            $conditions['CustomerUser.id IN'] = $arr_ust_with_itinerary;
        }

        $customer_users_pending = $this->CustomerUser->find('list', [
            'conditions' => $conditions,
        ]);

        $suppliersCount = $this->OrderItem->find('count', [
            'conditions' => ['OrderItem.order_id' => $id],
            'joins' => [
                [
                    'table'=> 'benefits',
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

        $customer_users_all = $this->CustomerUser->find('list', [
            'conditions' => ['CustomerUser.customer_id' => $order['Order']['customer_id']],
        ]);

        $benefits = $this->Benefit->find('list', ['fields' => ['id', 'complete_name'], 'order' => ['cast(Benefit.code as unsigned)' => 'asc']]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => '', 'Alterar Pedido' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items', 'progress'));
        $this->set(compact('customer_users_pending', 'suppliersCount', 'usersCount', 'income', 'customer_users_all', 'benefits'));

        $this->render("add");
    }

    public function changeStatusToSent($id)
    {
        $this->autoRender = false;

        $order = ['Order' => []];
        $order['Order']['id'] = $id;
        $order['Order']['status_id'] = 84;
        $order['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");
        $order['Order']['validation_date'] = date('Y-m-d');

        if ($this->Order->save($order)) {

            $order = $this->Order->findById($id);

            $bankTicket = $this->BankTicket->find('first', [
                'conditions' => ['BankTicket.status_id' => 1]
            ]);

            $this->Income->create();
            $income = [];
            
            $income['Income']['order_id'] = $id;
            $income['Income']['parcela'] = 1;
            $income['Income']['status_id'] = 15;
            $income['Income']['bank_account_id'] = $bankTicket['Bank']['id'];
            $income['Income']['customer_id'] = $order['Order']['customer_id'];
            $income['Income']['name'] = 'Conta a receber - Pedido ' . $order['Order']['id'];
            $income['Income']['valor_multa'] = $bankTicket['BankTicket']['multa_boleto_nao_formatada'];
            $income['Income']['valor_total'] = $order['Order']['subtotal'];
            $income['Income']['vencimento'] = date('d/m/Y', strtotime(' + 3 day'));;
            $income['Income']['data_competencia'] = date('01/m/Y');
            $income['Income']['created'] = date('Y-m-d H:i:s');
            $income['Income']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->Income->save($income);

            $this->Flash->set(__('O Pedido enviado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'edit/' . $id]);
        } else {
            $this->Flash->set(__('O Pedido não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
        }
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

        $customerItineraries = $this->CustomerUserItinerary->find('all', [
            'conditions' => ['CustomerUserItinerary.customer_user_id' => $customerUserId],
        ]);

        $this->processItineraries($customerItineraries, $orderId, $workingDays, $order['Order']['order_period_from'], $order['Order']['order_period_to']);

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);

        $this->Flash->set(__('Beneficiário incluído com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/' . $orderId]);
    }

    public function updateWorkingDays()
    {
        $this->autoRender = false;
        
        $itemId = $this->request->data['orderItemId'];

        $orderItem = $this->OrderItem->findById($itemId);

        if($this->request->data['newValue'] == 'working_days'){
            $workingDays = $this->request->data['newValue'];
            $orderItem['OrderItem']['working_days'] = $workingDays;
            $var = $orderItem['OrderItem']['var'];
        } else {
            $workingDays = $orderItem['OrderItem']['working_days'];
            $var_raw = $this->request->data['newValue'];
            $var = str_replace(".", "", $var_raw);
            $var = (float)str_replace(",", ".", $var);
            $orderItem['OrderItem']['var'] = $var_raw;
        }
        $orderItem['OrderItem']['updated_user_id'] = CakeSession::read("Auth.User.id");
        $orderItem['OrderItem']['subtotal'] = $workingDays * $orderItem['OrderItem']['price_per_day'];
        $orderItem['OrderItem']['subtotal'] = $orderItem['OrderItem']['subtotal'] - $var;

        $benefitId = $orderItem['CustomerUserItinerary']['benefit_id'];
        $benefit = $this->Benefit->findById($benefitId);
        $transferFeePercentage = $benefit['Supplier']['transfer_fee_percentage'];
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

    public function removeOrderItem($orderId, $customerUserId)
    {
        $this->autoRender = false;

        $this->OrderItem->unbindModel(
            ['belongsTo' => ['Order', 'CustomerUserItinerary', 'CustomerUser']]
        );
        
        $this->OrderItem->updateAll(
            ['OrderItem.data_cancel' => 'CURRENT_DATE', 'OrderItem.usuario_id_cancel' => CakeSession::read("Auth.User.id")],
            ['OrderItem.order_id' => $orderId, 'OrderItem.customer_user_id' => $customerUserId]
        );

        $this->OrderItem->bindModel(
            ['belongsTo' => ['Order', 'CustomerUserItinerary', 'CustomerUser']]
        );

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);

        $this->redirect('/orders/edit/' . $orderId);
    }

    public function addItinerary(){
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
            
            $this->processItineraries($customerItineraries, $orderId, $order['Order']['working_days'], $order['Order']['order_period_from'], $order['Order']['order_period_to']);  

            $this->Order->id = $orderId;
            $this->Order->reProcessAmounts($orderId);

            $this->Flash->set(__('Itinerário adicionado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect('/orders/edit/'.$orderId);
        } else {
            $this->Flash->set(__('Itinerário não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
        }
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
}
