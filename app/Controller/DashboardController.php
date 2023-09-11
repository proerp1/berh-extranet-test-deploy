<?php
class DashboardController extends AppController
{
  public $helpers = array('Html', 'Form');
  public $components = array('Paginator', 'Permission', 'Email');
  public $uses = ['Customer', 'Order', 'OrderItem', 'Proposal', 'Seller'];

  public function beforeFilter()
  {
    parent::beforeFilter();
  }

  public function index()
  {
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

  public function add($clienteID = null)
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
  }

  public function produto()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Produto";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function financeiro()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Financeiro";

    $incomes = $this->Income->find("all", [
      "conditions" => ["Income.status_id" => [19, 20]],
      "fields" => ["sum(Income.valor_bruto) as total", 'count(Income.id) as qtd_total']
    ]);
    $incomes = $incomes[0][0];
    $totIn = $this->Income->find("all", ["fields" => ["sum(Income.valor_bruto) as total", 'count(Income.id) as qtd_total']]);
    $totIn = $totIn[0][0];

    $porcIn = 0;
    if ($totIn['total'] != null) {
      $porcIn = ($incomes['total'] / $totIn['total']) * 100;
    }

    $outcomesB = $this->Outcome->find("all", [
      "fields" => ["sum(Outcome.valor_bruto) as total", 'count(Outcome.id) as qtd_total', 'Outcome.status_id'],
      'group' => ['Outcome.status_id']
    ]);
    $totOut = $this->Outcome->find("all", ["fields" => ["sum(Outcome.valor_bruto) as total", 'count(Outcome.id) as qtd_total']]);
    $totOut = $totOut[0][0];

    $outcomes = [];
    $totPend = 0;
    for ($i = 0; $i < count($outcomesB); $i++) {
      $porc = ($outcomesB[$i][0]['total'] / $totOut['total']) * 100;
      $outcomes[$outcomesB[$i]['Outcome']['status_id']] = ['total' => $outcomesB[$i][0]['total'], 'qtd_total' => $outcomesB[$i][0]['qtd_total'], 'porc' => $porc];

      if (in_array($outcomesB[$i]['Outcome']['status_id'], [15, 16])) {
        $totPend += $outcomesB[$i][0]['total'];
      }
    }

    $porcPendOut = 0;
    if ($totOut['total'] != null) {
      $porcPendOut = ($totPend / $totOut['total']) * 100;
    }

    $data = $this->Outcome->find("all", ["conditions" => ["Outcome.status_id" => [15, 16]], 'limit' => 10, 'order' => ["Outcome.id" => "desc"]]);

    $status = $this->Status->find('list', array('conditions' => array('Status.categoria' => 4)));

    $this->set(compact('breadcrumb', 'action', 'incomes', 'outcomes', 'porcIn', 'porcPendOut', 'data', 'totPend', 'status'));
  }

  public function comercial()
  {
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Comercial";

    if (CakeSession::read("Auth.User.is_seller") != true && CakeSession::read("Auth.User.Group.name") != 'Administrador') {
      $this->redirect('/customers');
    }

    $cond = [
      'Customer.seller_id' => CakeSession::read('Auth.User.id'),
      'Order.order_period_from >=' => date('Y-m-01'),
      'Order.order_period_to <=' => date('Y-m-t'),
    ];
    if(CakeSession::read("Auth.User.Group.name") == 'Administrador'){
      unset($cond['Customer.seller_id']);
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
      $allGoals = $this->Seller->find("all", [
        "conditions" => [
          'Seller.status_id' => 1,
          'Seller.is_seller' => 1,
        ],
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
      unset($propCond['Customer.seller_id']);
    }

    $propMonths = $this->Proposal->find("all", [
      "conditions" => $propCond,
      "fields" => ["DATE_FORMAT(Proposal.expected_closing_date, '%m/%Y') as month"],
      'group' => ["DATE_FORMAT(Proposal.expected_closing_date, '%m/%Y')"],
    ]);

    $is_admin = CakeSession::read("Auth.User.Group.name") == 'Administrador';

    $this->set(compact('breadcrumb', 'action', 'groupedOrders', 'totalSales', 'goal'));
    $this->set(compact('percentageLeft', 'totalSalesRaw', 'dailyGoal', 'totalSalesPreview'));
    $this->set(compact('goalLeft', 'totalSalesEstimate', 'topSuppliers', 'proposals', 'propMonths', 'is_admin'));
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
