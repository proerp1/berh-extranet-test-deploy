<?php
class DashboardController extends AppController {
  public $helpers = array('Html', 'Form');
  public $components = array('Paginator', 'Permission', 'Email');
  public $uses = ['Customer', 'Suggestion', 'ContactDirector', 'Outcome', 'Income', 'PedidoCompra', 'Status', 'NaturezaOperacao', 'Estabelecimento', 'CondicaoPagamento','CliRemTri', 'Portador', 'Transportador','Vendedor', 'Pedido', 'CustomerAddress', 'Limite'];

  public function beforeFilter() { 
    parent::beforeFilter(); 
  }

  public function index(){
		$breadcrumb = ["Dashboard" => "/"];
    $action = "Principal";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function oportunidade(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Oportunidades";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function outros(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Outros";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function resumo(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Resumo";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function compras(){
    $breadcrumb = ["Compras" => "/"];
    $action = "Compras";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function fornecedores(){
    $breadcrumb = ["Fornecedores" => "/"];
    $action = "Fornecedores";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function expedicao(){
    $breadcrumb = ["Expedição" => "/"];
    $action = "Expedição";

    $this->set(compact('breadcrumb', 'action'));
  }


  public function cliente(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Cliente";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function orcamentos(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Orçamentos";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function add($clienteID = null){

    $breadcrumb = ["Dashboard" => "/"];
    $action = "add";

    if ($this->request->is('post')) {
			$this->Pedido->create();
			
      $this->request->data['Pedido']['created'] = date();
			$this->request->data['Pedido']['usuarioID'] = CakeSession::read("Auth.User.id");

			if ($this->Pedido->save($this->request->data)) {
				$this->Session->setFlash(__('O registro foi salvo com sucesso'), 'default', ['class' => 'alert alert-success']);
				$this->redirect(['action'=> 'edit/'.$this->request->data['Pedido']['clienteID'].'/'.$this->Pedido->id]);
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

    $this->set(compact('breadcrumb', 'action', 'naturezas','estabelecimentos','condicoesPagamento','clis','portadores','transportadores', 'customers', 'customersPayment','vendedores','buscaPedido','buscaCliente','buscaEndereco','buscaLimite'));
  
  }

  public function edit($clienteID, $pedidoID = null) {
    
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

    $this->set(compact('breadcrumb', 'action', 'naturezas','estabelecimentos','condicoesPagamento','clis','portadores','transportadores', 'customers', 'customersPayment','vendedores','buscaPedido','buscaCliente','buscaEndereco','buscaLimite'));
		
    $this->render("add");
	}

  public function produto(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Produto";

    $this->set(compact('breadcrumb', 'action'));
  }

  public function financeiro(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Financeiro";

    $incomes = $this->Income->find("all", ["conditions" => ["Income.status_id" => [19, 20]], 
                                           "fields" => ["sum(Income.valor_bruto) as total", 'count(Income.id) as qtd_total']
                                          ]);
    $incomes = $incomes[0][0];
    $totIn = $this->Income->find("all", ["fields" => ["sum(Income.valor_bruto) as total", 'count(Income.id) as qtd_total']]);
    $totIn = $totIn[0][0];

    $porcIn = 0;
    if ($totIn['total'] != null) {
      $porcIn = ($incomes['total']/$totIn['total'])*100;
    }
    
    $outcomesB = $this->Outcome->find("all", ["fields" => ["sum(Outcome.valor_bruto) as total", 'count(Outcome.id) as qtd_total', 'Outcome.status_id'],
                                              'group' => ['Outcome.status_id']
                                             ]);
    $totOut = $this->Outcome->find("all", ["fields" => ["sum(Outcome.valor_bruto) as total", 'count(Outcome.id) as qtd_total']]);
    $totOut = $totOut[0][0];

    $outcomes = [];
    $totPend = 0;
    for ($i=0; $i < count($outcomesB); $i++) { 
      $porc = ($outcomesB[$i][0]['total']/$totOut['total'])*100;
      $outcomes[$outcomesB[$i]['Outcome']['status_id']] = ['total' => $outcomesB[$i][0]['total'], 'qtd_total' => $outcomesB[$i][0]['qtd_total'], 'porc' => $porc];

      if (in_array($outcomesB[$i]['Outcome']['status_id'], [15,16])) {
        $totPend += $outcomesB[$i][0]['total'];
      }
    }

    $porcPendOut = 0;
    if ($totOut['total'] != null) {
      $porcPendOut = ($totPend/$totOut['total'])*100;
    }

    $data = $this->Outcome->find("all", ["conditions" => ["Outcome.status_id" => [15, 16]], 'limit' => 10, 'order' => ["Outcome.id" => "desc"]]);

    $status = $this->Status->find('list', array('conditions' => array('Status.categoria' => 4)));

    $this->set(compact('breadcrumb', 'action', 'incomes', 'outcomes', 'porcIn', 'porcPendOut', 'data', 'totPend', 'status'));
  }

  public function comercial(){
    $breadcrumb = ["Dashboard" => "/"];
    $action = "Comercial";

    $this->set(compact('breadcrumb', 'action'));
  }
}