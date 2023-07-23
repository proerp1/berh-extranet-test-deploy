<?php
class OrdersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Order', 'Customer', 'CustomerUserItinerary', 'Benefit', 'OrderItem', 'CustomerUserVacation'];

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
        $this->set(compact('data', 'action', 'breadcrumb', 'customers'));
    }

    public function createOrder() {
        $this->autoRender = false;
        $customerId = $this->request->data['customer_id'];
        $workingDays = $this->request->data['working_days'];
        $period = '01/'.$this->request->data['period'];

        if ($this->request->is('post')) {
            $customer = $this->Customer->findById($customerId);
            $commissionFeePercentage = $customer['Customer']['commission_fee_percentage'];
    
            $customerItineraries = $this->CustomerUserItinerary->find('all', [
                'conditions' => ['CustomerUserItinerary.customer_id' => $customerId],
                'recursive' => 2
            ]);
    
            $orderData = [
                'customer_id' => $customerId,
                'working_days' => $workingDays,
                'user_creator_id' => CakeSession::read("Auth.User.id"),
                'order_period' => $period,
            ];
    
            $this->Order->create();
            if ($this->Order->save($orderData)) {
                $orderId = $this->Order->getLastInsertId();
    
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
    
                $commissionFee = $totalSubtotal * ($commissionFeePercentage / 100);
    
                $this->Order->id = $orderId;
                $this->Order->save([
                    'transfer_fee' => $totalTransferFee,
                    'subtotal' => $totalSubtotal,
                    'commission_fee' => $commissionFee,
                    'total' => $totalOrder + $commissionFee + $totalTransferFee,
                ]);
    
                $this->Flash->set(__('Pedido gerado com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('Falha ao criar pedido. Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
    
            $this->redirect(['action' => 'index']);
        }
    }

    public function edit($id = null)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Order->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Order->validates();
            $this->request->data['Order']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Order->save($this->request->data)) {
                $this->Flash->set(__('O Pedido foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O Pedido não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Order->validationErrors;
        $this->request->data = $this->Order->read();
        $order = $this->Order->findById($id);
        $this->Order->validationErrors = $temp_errors;

        $items = $this->Paginator->paginate('OrderItem', ['and' => ['Order.id' => $id]]);

        $action = 'Pedido';
        $breadcrumb = ['Cadastros' => '', 'Pedido' => '', 'Alterar Pedido' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'order', 'items'));
        
        $this->render("add");
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
