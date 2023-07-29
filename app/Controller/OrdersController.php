<?php
class OrdersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Order', 'Customer', 'CustomerUserItinerary', 'Benefit', 'OrderItem', 'CustomerUserVacation', 'CustomerUser'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Order.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(16, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Order.name LIKE' => "%".$_GET['q']."%", 'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('Order', $condition);
        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario']]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'customers', 'status'));
    }

    public function createOrder() {
        $this->autoRender = false;
        $customerId = $this->request->data['customer_id'];
        $workingDays = $this->request->data['working_days'];
        $period = '01/'.$this->request->data['period'];

        if ($this->request->is('post')) {    
            $customerItineraries = $this->CustomerUserItinerary->find('all', [
                'conditions' => ['CustomerUserItinerary.customer_id' => $customerId],
                'recursive' => 2
            ]);
    
            $orderData = [
                'customer_id' => $customerId,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period' => $period,
                'status_id' => 83,
            ];
    
            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();
    
                $this->processItineraries($customerItineraries, $orderId, $workingDays, $period);
    
                $this->Order->id = $orderId;
                $this->Order->reProcessAmounts($orderId);
    
                $this->Flash->set(__('Pedido gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('Falha ao criar pedido. Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
    
            $this->redirect(['action' => 'index']);
        }
    }

    public function processItineraries($customerItineraries, $orderId, $workingDays, $period){
        $totalTransferFee = 0;
        $totalSubtotal = 0;
        $totalOrder = 0;

        foreach ($customerItineraries as $itinerary) {
            $pricePerDay = $itinerary['CustomerUserItinerary']['price_per_day_not_formated'];
            $workingDaysUser = $this->CustomerUserVacation->calculateWorkingDays($itinerary['CustomerUserItinerary']['customer_user_id'], $period);
            
            if($workingDaysUser > $workingDays){
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
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Order->id = $id;
        $old_order = $this->Order->read();
        if ($this->request->is(['post', 'put'])) {
            
            if($old_order['Order']['status_id'] < 85){
                $order = ['Order' => []];
                $order['Order']['id'] = $id;
                $order['Order']['observation'] = $this->request->data['Order']['observation'];
                $order['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");
            }

            if ($this->Order->save($order)) {
                $this->Flash->set(__('O Pedido foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'edit/'.$id]);
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

        if(!empty($arr_cst_in_order)){
            $conditions['CustomerUser.id NOT IN'] = $arr_cst_in_order;
        }

        $users_with_itinerary = $this->CustomerUserItinerary->find('all', [
            'conditions' => [
                'CustomerUserItinerary.customer_id' => $order['Order']['customer_id'],
                'CustomerUserItinerary.customer_user_id NOT IN' => $arr_cst_in_order
            ],
            'group' => ['CustomerUserItinerary.customer_user_id'],
            'fields' => ['CustomerUserItinerary.customer_user_id']
        ]);

        $arr_ust_with_itinerary = Hash::extract($users_with_itinerary, '{n}.CustomerUserItinerary.customer_user_id');

        if(!empty($arr_ust_with_itinerary)){
            $conditions['CustomerUser.id IN'] = $arr_ust_with_itinerary;
        }

        $customer_users_pending = $this->CustomerUser->find('list', [
            'conditions' => $conditions,
        ]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => '', 'Alterar Pedido' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items', 'progress', 'customer_users_pending'));
        
        $this->render("add");
    }

    public function changeStatusToSent($id){
        $this->autoRender = false;

        $order = ['Order' => []];
        $order['Order']['id'] = $id;
        $order['Order']['status_id'] = 84;
        $order['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");

        if ($this->Order->save($order)) {
            $this->Flash->set(__('O Pedido enviado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'edit/'.$id]);
        } else {
            $this->Flash->set(__('O Pedido não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
        }
    }

    public function addCustomerUserToOrder(){
        $orderId = $this->request->data['order_id'];
        $customerUserId = $this->request->data['customer_user_id'];
        $workingDays = $this->request->data['working_days'];

        $order = $this->Order->findById($orderId);

        $customerItineraries = $this->CustomerUserItinerary->find('all', [
            'conditions' => ['CustomerUserItinerary.customer_user_id' => $customerUserId],
        ]);

        $this->processItineraries($customerItineraries, $orderId, $workingDays, '01/'.$order['Order']['order_period']);

        $this->Order->id = $orderId;
        $this->Order->reProcessAmounts($orderId);

        $this->Flash->set(__('Beneficiário incluído com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'edit/'.$orderId]);
    }

    public function delete($id)
    {
        $this->Permission->check(16, "excluir") ? "" : $this->redirect("/not_allowed");
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
