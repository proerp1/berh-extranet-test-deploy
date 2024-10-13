<?php
class DashboardController extends AppController
{
  public $helpers = array('Html', 'Form');
  public $components = array('Paginator', 'Permission', 'Email');
  public $uses = ['Customer', 'Order', 'OrderItem', 'Proposal', 'Seller', 'Income', 'Outcome', 'Status', 'User'];

  public function beforeFilter()
  {
    parent::beforeFilter();
  }

  public function index()
  {
    $this->Permission->check(4, "leitura") ? "" : $this->redirect("/not_allowed");
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Principal";

    if (CakeSession::read("Auth.User.is_seller")) {
      $this->redirect('/dashboard/comercial');
    }

    $this->set(compact('breadcrumb', 'action'));
  }

  public function oportunidade()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Oportunidades";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function outros()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Outros";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function resumo()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Resumo";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function compras()
  {
    $breadcrumb = ["Compras" => "/"];
    $action = "Compras";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function fornecedores()
  {
    $breadcrumb = ["Fornecedores" => "/"];
    $action = "Fornecedores";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function expedicao()
  {
    $breadcrumb = ["Expedição" => "/"];
    $action = "Expedição";

    $this->set(compact('breadcrumb', 'action'));
  }


  public function cliente()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Cliente";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function orcamentos()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Orçamentos";

    $this->set(compact('breadcrumb', 'action'));
  }

  /*public function add($clienteID = null)
  {

    $breadcrumb = ["Dashboard" => "/"];
    $action = "add";

    if ($this->request->is('post')) {
      $this->Pedido->create();

      $this->request->data['Pedido']['created'] = date();
      $this->request->data['Pedido']['usuarioID'] = CakeSession::read("Auth.User.id");

      if ($this->Pedido->save($this->request->data)) {
        $this->Session->setFlash(__('O registro foi salvo com sucesso'), 'default', ['class' => 'alert alert-success']);
        $this->redirect(['action' => 'edit/' . $this->request->data['Pedido']['clienteID'] . '/' . $this->Pedido->id]);
      } else {
        $this->Session->setFlash(__('O registro não pode ser salvo, por favor tente novamente.'), 'default', ['class' => 'alert alert-danger']);
      }
    }

    $naturezas = $this->NaturezaOperacao->find('list');
    $estabelecimentos = $this->Estabelecimento->find('list');
    $condicoesPagamento = $this->CondicaoPagamento->find('list');
    $clis = $this->CliRemTri->find('list');
    $portadores = $this->Portador->find('list');
    $transportadores = $this->Transportador->find('list');
    $customers = $this->Customer->find('list', array('conditions' => array('empresa_id' => CakeSession::read("Auth.User.empresa_selecionada"))));
    $customersPayment = $this->Customer->find('list', array('conditions' => array('empresa_id' => CakeSession::read("Auth.User.empresa_selecionada"))));
    $vendedores = $this->Vendedor->find('list');
    $buscaPedido = array(0);
    $buscaCliente = $this->Customer->getDataCustomer($clienteID);
    $buscaEndereco = $this->CustomerAddress->getDataAddressType($clienteID, 1);
    $buscaLimite = $this->Limite->getDataLimite($clienteID);

    $this->set(compact('breadcrumb', 'action', 'naturezas', 'estabelecimentos', 'condicoesPagamento', 'clis', 'portadores', 'transportadores', 'customers', 'customersPayment', 'vendedores', 'buscaPedido', 'buscaCliente', 'buscaEndereco', 'buscaLimite'));
  }

  public function edit($clienteID, $pedidoID = null)
  {

    $breadcrumb = ["Dashboard" => "/"];

    $this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
    $this->Pedido->id = $pedidoID;

    if ($this->request->is('post')) {

      $this->request->data['Pedido']['user_updated_id'] = CakeSession::read("Auth.User.id");
      $this->request->data['Pedido']['updated'] = date('Y-m-d H:i:s');

      if ($this->Pedido->save($this->request->data)) {
        $this->Session->setFlash(__('O registro foi alterado com sucesso.'), 'default', ['class' => 'alert alert-success']);
      } else {
        $this->Session->setFlash(__('O registro não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => 'alert alert-danger']);
      }
    }

    $naturezas = $this->NaturezaOperacao->find('list');
    $estabelecimentos = $this->Estabelecimento->find('list');
    $condicoesPagamento = $this->CondicaoPagamento->find('list');
    $clis = $this->CliRemTri->find('list');
    $portadores = $this->Portador->find('list');
    $transportadores = $this->Transportador->find('list');
    $customers = $this->Customer->find('list', array('conditions' => array('empresa_id' => CakeSession::read("Auth.User.empresa_selecionada"))));
    $customersPayment = $this->Customer->find('list', array('conditions' => array('empresa_id' => CakeSession::read("Auth.User.empresa_selecionada"))));
    $vendedores = $this->Vendedor->find('list');
    $buscaPedido = $this->Pedido->getDataPedido($this->Pedido->id);
    $buscaCliente = $this->Customer->getDataCustomer($clienteID);
    $buscaEndereco = $this->CustomerAddress->getDataAddressType($clienteID, 1);
    $buscaLimite = $this->Limite->getDataLimite($clienteID);

    $this->set("action", "edit");

    $this->set(compact('breadcrumb', 'action', 'naturezas', 'estabelecimentos', 'condicoesPagamento', 'clis', 'portadores', 'transportadores', 'customers', 'customersPayment', 'vendedores', 'buscaPedido', 'buscaCliente', 'buscaEndereco', 'buscaLimite'));

    $this->render("add");
  }*/

  public function produto()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Produto";

    $this->set(compact('breadcrumb', 'action'));
  }


  
  public function financeiro()
{
    $breadcrumb = ['Dashboard' => '/'];
        $action = 'Financeiro';

        $totalReceived = $this->Order->find('all', [
            'conditions' => [
                'Order.status_id' => 87,
                //'Order.customer_id' => CakeSession::read('Auth.CustomerUser.customer_id'),
                'Order.order_period_from >=' => date('Y-m-01'),
                'Order.order_period_to <=' => date('Y-m-t'),
            ],
            'fields' => ['sum(Order.total) as total'],
        ]);
        $totalReceivedRaw = $totalReceived[0][0]['total'];
        $totalReceived = number_format($totalReceivedRaw, 2, ',', '.');

        $totalDiscount = $this->OrderBalance->find('first', [
            'contain' => ['Order'],
            'conditions' => [
                'Order.status_id' => 87,
                //'Order.customer_id' => CakeSession::read('Auth.CustomerUser.customer_id'),
                'Order.order_period_from >=' => date('Y-m-01'),
                'Order.order_period_to <=' => date('Y-m-t'),
                'OrderBalance.tipo' => 1
            ],
            'fields' => ['sum(OrderBalance.total) as total'],
        ]);
        $totalDiscountRaw = $totalDiscount[0]['total'];
        $totalDiscount = number_format($totalDiscountRaw, 2, ',', '.');

        $this->set(compact('breadcrumb', 'action', 'totalReceived', 'totalDiscount', 'totalReceivedRaw', 'totalDiscountRaw'));
}



public function getEvolucaoPedidos()
    {
        $this->autoRender = false;

        $this->Order->unbindModel([
            'hasMany' => ['OrderItem'],
        ]);

        $evolucaoPedidos = $this->Order->find('all', [
            'fields' => [
                'sum(Order.total) as total',
                '(select sum(total) from order_balances b where b.order_id = Order.id and b.tipo = 1 and b.data_cancel = "1901-01-01 00:00:00") as economia',
                "DATE_FORMAT(Order.order_period_from, '%m/%Y') as mes",
            ],
            'conditions' => [
                'Order.order_period_from >=' => date('Y-01-01'),
                'Order.order_period_to <=' => date('Y-12-31'),
                'Order.status_id' => 87,
            ],
            'group' => ["DATE_FORMAT(Order.order_period_from, '%m/%Y')"],
            'order' => ["DATE_FORMAT(Order.order_period_from, '%m/%Y')"],
        ]);

        $data = [];
        foreach ($evolucaoPedidos as $value) {
            $data[] = [
                'mesAno' => $value[0]['mes'],
                'totalPedido' => (float) $value[0]['total'],
                'totalEconomia' => (float) $value[0]['economia'],
            ];
        }

        $result = [
            'data' => $data,
        ];

        echo json_encode($result);
    }

public function getRankingOperadoras()
{
    $this->autoRender = false;

    // Carregar o modelo OrderItem
    $this->loadModel('OrderItem');

    // Buscar o ranking de operadoras
    $rankingOperadoras = $this->OrderItem->find('all', [
        'fields' => ['sum(Order.total) as total', 'Supplier.nome_fantasia'],
        'joins' => [
            [
                'table' => 'benefits',
                'alias' => 'Benefit',
                'type' => 'INNER',
                'conditions' => ['CustomerUserItinerary.benefit_id = Benefit.id'],
            ],
            [
                'table' => 'suppliers',
                'alias' => 'Supplier',
                'type' => 'INNER',
                'conditions' => ['Benefit.supplier_id = Supplier.id'],
            ],
        ],
        'conditions' => [
            'Order.order_period_from >=' => date('Y-m-01'),
            'Order.order_period_to <=' => date('Y-m-t'),
            'Order.status_id' => 87,
            // Removendo filtros por customer_id 
        ],
        'group' => ['Supplier.nome_fantasia'],
        'limit' => 10,
        'order' => ['total' => 'DESC'],
    ]);

    $header = [];
    $data = [];
    foreach ($rankingOperadoras as $value) {
        $header[] = $value['Supplier']['nome_fantasia'];
        $data[] = (float) $value[0]['total'];
    }

    $result = [
        'header' => $header,
        'data' => $data,
    ];

    echo json_encode($result);
}

public function getRadarDash()
{
    $this->autoRender = false;

    // Load the OrderItem model
    $this->loadModel('OrderItem');

    // Fetch total orders by department without filtering by CustomerUser ID
    $ordersByDepartment = $this->OrderItem->find('all', [
        'fields' => ['sum(Order.total) as total', 'CustomerDepartment.name'],
        'joins' => [
            [
                'table' => 'customer_departments',
                'alias' => 'CustomerDepartment',
                'type' => 'INNER',
                'conditions' => [
                    'Order.customer_departments_id = CustomerDepartment.id', // Remove CustomerUser
                ],
            ],
        ],
        'conditions' => [
            'Order.order_period_from >=' => date('Y-m-01'),
            'Order.order_period_to <=' => date('Y-m-t'),
            'Order.status_id' => 87,
            // Removed CustomerUser ID condition
        ],
        'group' => ['CustomerDepartment.name'],
    ]);

    // Fetch total orders by cost center without filtering by CustomerUser ID
    $ordersByCC = $this->OrderItem->find('all', [
        'fields' => ['sum(Order.total) as total', 'CostCenter.name'],
        'joins' => [
            [
                'table' => 'cost_center',
                'alias' => 'CostCenter',
                'type' => 'INNER',
                'conditions' => [
                    'Order.customer_cost_center_id = CostCenter.id', // Remove CustomerUser
                ],
            ],
        ],
        'conditions' => [
            'Order.order_period_from >=' => date('Y-m-01'),
            'Order.order_period_to <=' => date('Y-m-t'),
            'Order.status_id' => 87,
            // Removed CustomerUser ID condition
        ],
        'group' => ['CostCenter.name'],
    ]);

    // Prepare department data
    $departmentHeader = [];
    $departmentData = [];
    foreach ($ordersByDepartment as $value) {
        $departmentHeader[] = $value['CustomerDepartment']['name'];
        $departmentData[] = (float) $value[0]['total'];
    }

    // Prepare cost center data
    $ccHeader = [];
    $ccData = [];
    foreach ($ordersByCC as $value) {
        $ccHeader[] = $value['CostCenter']['name'];
        $ccData[] = (float) $value[0]['total'];
    }

    // Prepare the final result
    $result = [
        'department' => [
            'header' => $departmentHeader,
            'data' => $departmentData,
        ],
        'costCenter' => [
            'header' => $ccHeader,
            'data' => $ccData,
        ],
    ];

    echo json_encode($result);
}


    

  public function comercial()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Comercial";

    if (CakeSession::read("Auth.User.is_seller") != true && CakeSession::read("Auth.User.Group.name") != 'Administrador') {
      $this->redirect('/customers');
    }

    $cond = [
      'Order.order_period_from >=' => date('Y-m-01'),
      'Order.order_period_to <=' => date('Y-m-t'),
    ];

    if(CakeSession::read("Auth.User.Group.name") == 'Administrador'){
      if(isset($_GET['s']) && $_GET['s'] != ''){
        $cond['Customer.seller_id'] = $_GET['s'];
      }
    } else {
      $cond['Customer.seller_id'] = CakeSession::read('Auth.User.id');
    }

    $orders = $this->Order->find("all", [
      "conditions" => $cond,
      "fields" => ["Order.*", "Customer.*"],
    ]);

    $groupedOrders = [];
    $totalSalesRaw = 0;
    $totalSalesPreviewRaw = 0;
    $totalSalesEstimateRaw = 0;
    foreach ($orders as $order) {
      $groupedOrders[$order['Order']['status_id']][] = $order;
      if ($order['Order']['status_id'] > 84) {
        $totalSalesRaw = $totalSalesRaw + $order['Order']['total_not_formated'];
      }

      if ($order['Order']['status_id'] == 83) {
        $totalSalesEstimateRaw = $totalSalesEstimateRaw + $order['Order']['total_not_formated'];
      }

      if ($order['Order']['status_id'] == 84) {
        $totalSalesPreviewRaw = $totalSalesPreviewRaw + $order['Order']['total_not_formated'];
      }
    }

    $totalSales = number_format($totalSalesRaw, 2, ',', '.');
    $totalSalesPreview = number_format($totalSalesPreviewRaw, 2, ',', '.');
    $totalSalesEstimate = number_format($totalSalesEstimateRaw, 2, ',', '.');


    $goal = CakeSession::read("Auth.User.sales_goal_not_formated");
    if(CakeSession::read("Auth.User.Group.name") == 'Administrador'){
      $condGoal = [
        'Seller.status_id' => 1,
        'Seller.is_seller' => 1,
      ];
      if(isset($_GET['s']) && $_GET['s'] != ''){
        $condGoal['Seller.id'] = $_GET['s'];
      }
      $allGoals = $this->Seller->find("all", [
        "conditions" => $condGoal,
        "fields" => ["sum(Seller.sales_goal) as total"],
      ]);
      $goal = $allGoals[0][0]['total'];
    }

    $percentageLeft = 0;
    $goalLeft = 0;
    if ($goal != null) {
      $percentageLeft = ($totalSalesRaw / $goal) * 100;
      $goalLeft = $goal - $totalSalesRaw;
      $goalLeft = $goalLeft < 0 ? 0 : $goalLeft;
    }

    $workingDaysCurrentMonth = $this->workingDays();
    $dailyGoal = 0;
    if ($goal != null) {
      $dailyGoal = ($goal / $workingDaysCurrentMonth);
    }

    $topSuppliers = $this->OrderItem->find("all", [
      "conditions" => $cond,
      "fields" => ["Supplier.nome_fantasia", "sum(OrderItem.total) as total"],
      'joins' => [
        [
          'table' => 'benefits',
          'alias' => 'Benefit',
          'type' => 'INNER',
          'conditions' => [
            'Benefit.id = CustomerUserItinerary.benefit_id',
          ]
        ],
        [
          'table' => 'suppliers',
          'alias' => 'Supplier',
          'type' => 'INNER',
          'conditions' => [
            'Supplier.id = Benefit.supplier_id',
          ]
        ],
        [
          'table' => 'customers',
          'alias' => 'Customer',
          'type' => 'INNER',
          'conditions' => [
            'Customer.id = Order.customer_id',
          ]
        ]
      ],
      'group' => ['Supplier.id'],
      'order' => ['total' => 'desc'],
      'limit' => 10
    ]);

    $proposals = $this->getProposals(date('m/Y'));

    $propCond = [
      'Customer.seller_id' => CakeSession::read('Auth.User.id'),
      'Proposal.created >=' => date('Y-m-01'),
      'Proposal.created <=' => date('Y-m-t'),
    ];
    if(CakeSession::read("Auth.User.Group.name") == 'Administrador'){
      if(isset($_GET['s']) && $_GET['s'] != ''){
        $propCond['Customer.seller_id'] = $_GET['s'];
      } else {
        unset($propCond['Customer.seller_id']);
      }
    }

    $propMonths = $this->Proposal->find("all", [
      "conditions" => $propCond,
      "fields" => ["DATE_FORMAT(Proposal.expected_closing_date, '%m/%Y') as month"],
      'group' => ["DATE_FORMAT(Proposal.expected_closing_date, '%m/%Y')"],
    ]);

    $is_admin = CakeSession::read("Auth.User.Group.name") == 'Administrador';

    $executivos = $this->User->find('list', ['conditions' => ['User.is_seller' => 1]]);

    $this->set(compact('breadcrumb', 'action', 'groupedOrders', 'totalSales', 'goal'));
    $this->set(compact('percentageLeft', 'totalSalesRaw', 'dailyGoal', 'totalSalesPreview'));
    $this->set(compact('goalLeft', 'totalSalesEstimate', 'topSuppliers', 'proposals', 'propMonths', 'is_admin', 'executivos'));
  }

  public function getProposalByMonth(){
    $this->autoRender = false;
    $this->layout = 'ajax';

    $month = $this->request->data('month');
    $proposals = $this->getProposals($month);

    echo json_encode($proposals);
  }

  private function getProposals($month){
    $month = explode('/', $month);
    $initalDate = $month[1] . '-' . $month[0] . '-01';
    $finalDate = date($month[1] . '-' . $month[0] .'-t');

    $cond = [
      'Customer.seller_id' => CakeSession::read('Auth.User.id'),
      'Proposal.expected_closing_date >=' => $initalDate,
      'Proposal.expected_closing_date <=' => $finalDate,
    ];
    if(CakeSession::read("Auth.User.Group.name") == 'Administrador'){
      unset($cond['Customer.seller_id']);
    }

    $proposals = $this->Proposal->find("all", [
      "conditions" => $cond,
      "fields" => ["Proposal.*"],
      'orderBy' => ['Proposal.expected_closing_date' => 'asc'],
    ]);

    return $proposals;
  }

  private function workingDays()
  {
    // Get the first day and last day of the current month
    $firstDay = date('Y-m-01');
    $lastDay = date('Y-m-t', strtotime($firstDay));

    // Initialize a counter for working days
    $workingDays = 0;

    // Loop through each day in the month
    $currentDate = $firstDay;
    while ($currentDate <= $lastDay) {
      // Check if the current day is a weekend (Saturday or Sunday)
      $dayOfWeek = date('N', strtotime($currentDate));
      if ($dayOfWeek < 6) {
        $workingDays++;
      }

      // Move to the next day
      $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    }

    return $workingDays;
  }
}
