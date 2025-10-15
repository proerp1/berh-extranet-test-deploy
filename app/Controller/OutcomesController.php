<?php
App::uses('ApiBtgPactual', 'Lib');

class OutcomesController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission', 'ExcelGenerator'];
	public $uses = ['TipoDocumento','Outcome', 'Status', 'Expense', 'BankAccount', 'CostCenter', 'Supplier', 'Log', 'PlanoConta', 'Resale', 'Docoutcome', 'Order','BankAccountType','BankCode','OutcomeOrder','Customer', 'OrderItem','CustomerUser'];

	public $paginate = [
        'Outcome' => [
            'fields' => [
                'Outcome.*',
                'Status.*',
                'Supplier.nome_fantasia',
                'UserUpdated.name',
                'Supplier.valor',
                'Supplier.unidade_tempo',
                "(SELECT c.nome_primario
                    FROM orders o 
                        INNER JOIN customers c ON o.customer_id = c.id  
                    WHERE o.id = Outcome.order_id  
                            AND c.data_cancel = '1901-01-01 00:00:00' 
                            AND o.data_cancel = '1901-01-01 00:00:00' 
                ) as nome_primario",
              	"(select IFNULL(group_concat(orders.id, ' - ', c.nome_primario SEPARATOR '<br>'), '')
                	from outcome_orders
                     	inner join orders on outcome_orders.order_id = orders.id
                     	inner join customers c on orders.customer_id = c.id
                   	where Outcome.id = outcome_orders.outcome_id
				) as orders",
				"(select IFNULL(group_concat(orders.id SEPARATOR '<br>'), '')
					from outcome_orders
						inner join orders on outcome_orders.order_id = orders.id
						inner join customers c on orders.customer_id = c.id
					where Outcome.id = outcome_orders.outcome_id
				) as order_ids"
            ],
            'limit' => 175,
            'order' => ['Outcome.created' => 'asc'],
            'paramType' => 'querystring',
        ]
	];

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function index()
	{
		$this->Permission->check(15, "leitura") ? "" : $this->redirect("/not_allowed");

		$limit = !empty($this->request->query('limit')) ? (int)$this->request->query('limit') : 50;

        $this->paginate['Outcome']['limit'] = $limit;
        $this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Outcome.resale_id' => CakeSession::read("Auth.User.resales")], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Outcome.observation LIKE' => "%".$_GET['q']."%",'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%", 'Outcome.doc_num LIKE' => "%".$_GET['q']."%", 'Outcome.name LIKE' => "%".$_GET['q']."%", 'BankAccount.name LIKE' => "%".$_GET['q']."%"]);
		}

		if(isset($_GET["t"]) and $_GET["t"] != ""){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
		}

		if (!empty($_GET["payment_method"])) {
			$condition['and']['Outcome.payment_method'] = $_GET["payment_method"];
		}

		if(isset($_GET['supplier_id']) && $_GET['supplier_id'] != "") {
			$condition['and'] = array_merge($condition['and'], ['Outcome.supplier_id' => $_GET['supplier_id']]);
		}

		if(isset($_GET['supplier_nome']) && $_GET['supplier_nome'] != "") {
			$condition['and'] = array_merge($condition['and'], ['Supplier.nome_fantasia LIKE' => "%".$_GET['supplier_nome']."%"]);
		}

		if(isset($_GET['order']) && $_GET['order'] != "") {
      $orderId = $_GET['order'];
			$condition['and'] = array_merge($condition['and'], ["Outcome.id in (select outcome_id from outcome_orders where order_id = $orderId)"]);
		}

		$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
		$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

		if($get_de != "" and $get_ate != ""){
			$de = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['de'])));
			$ate = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['ate'])));

			if(isset($_GET["t"]) and $_GET["t"] == 13){
				$condition['and'] = array_merge($condition['and'], ['Outcome.data_pagamento >=' => $de, 'Outcome.data_pagamento <=' => $ate]);
			} else {
				$condition['and'] = array_merge($condition['and'], ['Outcome.vencimento >=' => $de, 'Outcome.vencimento <=' => $ate]);
			}
		}

		$get_created_de = isset($_GET["created_de"]) ? $_GET["created_de"] : '';
        $get_created_ate = isset($_GET["created_ate"]) ? $_GET["created_ate"] : '';

        if ($get_created_de != "" && $get_created_ate != "") {
            $created_de = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $_GET['created_de'])));
            $created_ate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $_GET['created_ate'])));

            $condition['and'] = array_merge($condition['and'], ['Outcome.created >=' => $created_de, 'Outcome.created <=' => $created_ate]);
        }

		$get_pagamento_de = isset($_GET["pagamento_de"]) ? $_GET["pagamento_de"] : '';
        $get_pagamento_ate = isset($_GET["pagamento_ate"]) ? $_GET["pagamento_ate"] : '';

        if ($get_pagamento_de != "" && $get_pagamento_ate != "") {
            $pagamento_de = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $_GET['pagamento_de'])));
            $pagamento_ate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $_GET['pagamento_ate'])));

            // Alterar a condição para filtrar pela coluna 'data_pagamento'
            $condition['and'] = array_merge($condition['and'], [
                'Outcome.data_pagamento >=' => $pagamento_de,
                'Outcome.data_pagamento <=' => $pagamento_ate
            ]);
		}

		if (isset($_GET['exportar'])) {
			$nome = 'contas_pagar_' . date('d_m_Y_H_i_s') . '.xlsx';

			$data = $this->Outcome->find('all', [
				'conditions' => $condition,
				'joins' => [
					[
						'table' => 'bank_codes',
						'alias' => 'BankCode',
						'type' => 'LEFT',
						'conditions' => ['BankCode.id = Supplier.bank_code_id']
					],
					[
						'table' => 'bank_account_types',
						'alias' => 'BankAccountType',
						'type' => 'LEFT',
						'conditions' => ['BankAccountType.id = Supplier.account_type_id'],
					]
				],
				'fields' => [
                        'BankCode.name',
                        'BankCode.code',
						'Supplier.*',
						'Status.name',
						'BankAccountType.description',
						'Outcome.*',
						'BankAccount.*',
          				"(select IFNULL(group_concat(orders.id, ' - ', c.nome_primario SEPARATOR '<br>'), '')
                			from outcome_orders
                     			inner join orders on outcome_orders.order_id = orders.id
                     			inner join customers c on orders.customer_id = c.id
                   			where Outcome.id = outcome_orders.outcome_id
						) as orders",
          				"(select IFNULL(group_concat(orders.id SEPARATOR '<br>'), '')
                			from outcome_orders
                     			inner join orders on outcome_orders.order_id = orders.id
                     			inner join customers c on orders.customer_id = c.id
                   			where Outcome.id = outcome_orders.outcome_id
						) as order_ids"
					]
				]
			);

			$this->ExcelGenerator->gerarExcelOutcome($nome, $data);

			$this->redirect("/files/excel/".$nome);
		}

		if (isset($_GET['exportarnibo'])) {
			$nome = 'nibo' . date('d_m_Y_H_i_s') . '.xlsx';

			$dataNibo = $this->Outcome->find('all', [
			'conditions' => $condition,

				'fields' => [
					'Outcome.*',
					'Supplier.*',
					'BankAccount.*',
					'Expense.name',
					'CostCenter.name'
				]
			]);

			$this->ExcelGenerator->gerarExcelNibo($nome, $dataNibo);

			$this->redirect("/files/excel/".$nome);
		}


		$saldo = 0;

		if (!empty($data) && is_array($data)) {
			foreach ($data as $item) {
				// Verificar se o índice 0 está definido no item atual
				if (isset($item[0]) && is_array($item[0]) && isset($item[0]['valor_total'])) {
					$saldo += $item[0]['valor_total'];
				}
			}
		}

		// Agora $saldo contém a soma dos 'valor_total' para os itens válidos em $data
		//echo "Saldo: " . $saldo;
		$total_outcome = 0;
		$pago_outcome = 0;

		$total_outcome = $this->Outcome->find('first', [
			'conditions' => $condition,
			'fields' => [
				'sum(Outcome.valor_total) as total_outcome',
			]
		]);

		$pago_outcome = $this->Outcome->find('first', [
			'conditions' => $condition,
			'fields' => [
				'sum(Outcome.valor_pago) as pago_outcome',
			]
		]);

		$payment_method = ['1' => 'Boleto','3' => 'Cartão de crédito','6' => 'Crédito em conta corrente','5' => 'Cheque','4' => 'Depósito','7' => 'Débito em conta','8' => 'Dinheiro','2' => 'Transfêrencia','9' => 'Desconto','11' => 'Pix','10' => 'Outros'];

		$aba_pago_id = 13;
		$aba_atual_id = isset($_GET['t']) ? $_GET['t'] : null;
		$exibir_segundo_card = $aba_atual_id == $aba_pago_id;

		$this->Paginator->settings['order'] = ['Outcome.created' => 'DESC'];

		$data = $this->Paginator->paginate('Outcome', $condition);
    $aba_status = [
      '11' => 'Programado',
      '12' => 'Aprovado',
      '116' => 'Em Processamento',
      '13' => 'Pago',
      '14' => 'Cancelado',
      '103' => 'Pendente',
    ];
		$status = array_map(function ($status, $id) {
      return ['Status' => [
        'id' => $id,
         'name' => $status,
      ]];
    }, $aba_status, array_keys($aba_status));

    $customers = $this->Customer->find('list', ['fields' => ['Customer.id', 'Customer.nome_primario']]);

    $orderArr = $this->Order->find('all', [
      'fields' => ['Order.id', 'Customer.nome_primario'],
      'contain' => ['Customer'],
      'order' => 'Order.id'
    ]);
    $orders = [];
    foreach ($orderArr as $order) {
      $orders[$order['Order']['id']] = $order['Order']['id'].' - '.$order['Customer']['nome_primario'];
    }

		$action = 'Contas a pagar';

		$this->set(compact('status', 'customers', 'limit', 'data', 'action', 'total_outcome', 'pago_outcome', 'exibir_segundo_card', 'aba_atual_id', 'aba_pago_id', 'payment_method', 'orders'));
	}

	public function add() {
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");
		if ($this->request->is('post')) {
			$this->Outcome->create();
			if($this->Outcome->validates()){
				$this->request->data['Outcome']['user_creator_id'] = CakeSession::read("Auth.User.id");
				$this->request->data['Outcome']['parcela'] = 1;
				$this->request->data['Outcome']['status_id'] = 11;
				if ($this->Outcome->save($this->request->data)) {
					$id_origem = $this->Outcome->id;
					if ($this->request->data['Outcome']['recorrencia'] == 1) {
						for ($i=0; $i < $this->request->data['Outcome']['quantidade']; $i++) {

							$year = substr($this->request->data['Outcome']['vencimento'],6,4);
							$month = substr($this->request->data['Outcome']['vencimento'],3,2);
							$date = substr($this->request->data['Outcome']['vencimento'],0,2);
							$data = $year."-".$month."-".$date;

							$cont = $i+1;
							$meses = $cont*$this->request->data['Outcome']["periodicidade"];

							$effectiveDate = date('d/m/Y', strtotime("+".$meses." months", strtotime($data)));

							$data_save = $this->request->data;
							$data_save['Outcome']['vencimento'] = $effectiveDate;
							$data_save['Outcome']['parcela'] = $cont+1;
							$data_save['Outcome']['conta_origem_id'] = $id_origem;

							$this->Outcome->create();
							$this->Outcome->save($data_save);
						}
					}

          for ($i = 0; $i <= count($this->request->data['Outcome']['order_ids']); $i++) {
            $orderId = $this->request->data['Outcome']['order_ids'][$i];
            $this->OutcomeOrder->create();
            $this->OutcomeOrder->save([
              'order_id' => $orderId,
              'outcome_id' => $id_origem,
            ]);
          }

					$this->Session->setFlash(__('A conta a pagar foi salva com sucesso'), 'default', array('class' => "alert alert-success"));
					$this->redirect(array('action' => 'edit', $id_origem)); // Redireciona para a ação de edição com o ID criado
				} else {
					$this->Session->setFlash(__('A conta a pagar não pode ser salva, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
				}
			} else {
				$this->Session->setFlash(__('A conta a pagar não pode ser salva, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$statuses = $this->Status->find('list', array('conditions' => array('Status.categoria' => 4)));
		$expenses = $this->Expense->find('list', ['conditions' => ['Expense.status_id' => 1], 'order' => 'Expense.name']);
		$bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1, 'BankAccount.id !=' => 5], 'order' => 'BankAccount.name']);
		$costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1, 'CostCenter.customer_id' => 0], 'order' => 'CostCenter.name']);
		$suppliers = $this->Supplier->find('list', ['conditions' => ['Supplier.status_id' => 1], 'order' => 'Supplier.nome_fantasia']);
		$planoContas = $this->PlanoConta->find('list', ['conditions' => ['PlanoConta.status_id' => 1], 'order' => ['PlanoConta.name' => 'asc']]);
		$resales = $this->Resale->find("list", ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], 'order' => ['Resale.nome_fantasia' => 'asc']]);

		$cancelarConta = $this->Permission->check(57, "escrita");

		$orderArr = $this->Order->find('all', [
            'fields' => ['Order.id', 'Customer.nome_primario'],
            'contain' => ['Customer'],
            'order' => 'Order.id'
        ]);
		$orders = [];
        foreach ($orderArr as $order) {
            $orders[$order['Order']['id']] = $order['Order']['id'].' - '.$order['Customer']['nome_primario'];
        }


		$action = 'Contas a pagar';
		$breadcrumb = ['Nova conta' => ''];
		$this->set("form_action", "add");
		$this->set(compact('statuses', 'expenses', 'bankAccounts', 'costCenters', 'suppliers', 'planoContas', 'cancelarConta', 'resales', 'action', 'breadcrumb', 'orders'));
	}


	public function edit($id = null) {
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");
		$this->Outcome->id = $id;
		if ($this->request->is('post')) {
			$this->Outcome->validates();
			$this->request->data['Outcome']['user_updated_id'] = CakeSession::read("Auth.User.id");
			$log_old_value = $this->request->data["log_old_value"];
			unset($this->request->data["log_old_value"]);

			$dados_log = [
				"old_value" => $log_old_value,
				"new_value" => json_encode($this->request->data),
				"route" => "outcomes/edit",
				"log_action" => "Alterou",
				"log_table" => "Outcome",
				"primary_key" => $id,
				"parent_log" => 0,
				"user_type" => "ADMIN",
				"user_id" => CakeSession::read("Auth.User.id"),
				"message" => "A conta a pagar foi alterada com sucesso",
				"log_date" => date("Y-m-d H:i:s"),
				"data_cancel" => "1901-01-01",
				"usuario_data_cancel" => 0,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			if ($this->Outcome->save($this->request->data)) {
				$this->Log->save($dados_log);

				$id_origem = $this->Outcome->id;
				if ($this->request->data['Outcome']['recorrencia'] == 1) {
					for ($i=0; $i < $this->request->data['Outcome']['quantidade']; $i++) {

						$year = substr($this->request->data['Outcome']['vencimento'],6,4);
						$month = substr($this->request->data['Outcome']['vencimento'],3,2);
						$date = substr($this->request->data['Outcome']['vencimento'],0,2);
						$data = $year."-".$month."-".$date;

						$cont = $i+1;
						$meses = $cont*$this->request->data['Outcome']["periodicidade"];

						$effectiveDate = date('d/m/Y', strtotime("+".$meses." months", strtotime($data)));

						$data_save = $this->request->data;
						$data_save['Outcome']['vencimento'] = $effectiveDate;
						$data_save['Outcome']['parcela'] = $cont+1;
						$data_save['Outcome']['conta_origem_id'] = $id_origem;

						$this->Outcome->create();
						$this->Outcome->save($data_save);
					}
				}

        $order_ids = $this->request->data['Outcome']['order_ids'];

        $this->OutcomeOrder->deleteAll([
          'OutcomeOrder.outcome_id' => $id,
        ]);
        for ($i = 0; $i < count($order_ids); $i++) {
          $orderId = $order_ids[$i];
          $this->OutcomeOrder->create();
          $this->OutcomeOrder->save([
            'order_id' => $orderId,
            'outcome_id' => $id,
          ]);
        }

				$this->Session->setFlash(__('A conta a pagar foi alterada com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect(array('action' => 'index/?'.$this->request->data['query_string']));
			} else {
				$this->Session->setFlash(__('A conta a pagar não pode ser alterada, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$temp_errors = $this->Outcome->validationErrors;
		$this->request->data = $this->Outcome->read();
		$this->Outcome->validationErrors = $temp_errors;

    $this->request->data['Outcome']['order_ids'] = collect($this->request->data['OutcomeOrder'])->map(function ($outcomeOrder) {
      return $outcomeOrder['order_id'];
    })->toArray();

		$statuses = $this->Status->find('list', array('conditions' => array('Status.categoria' => 4)));
		$expenses = $this->Expense->find('list', ['conditions' => ['Expense.status_id' => 1], 'order' => 'Expense.name']);
		$bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1, 'BankAccount.id !=' => 5], 'order' => 'BankAccount.name']);
		$costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1, 'CostCenter.customer_id' => 0], 'order' => 'CostCenter.name']);
		$suppliers = $this->Supplier->find('list', ['conditions' => ['Supplier.status_id' => 1], 'order' => 'Supplier.nome_fantasia']);
		$planoContas = $this->PlanoConta->find('list', ['conditions' => ['PlanoConta.status_id' => 1], 'order' => ['PlanoConta.name' => 'asc']]);
		$resales = $this->Resale->find("list", ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], 'order' => ['Resale.nome_fantasia' => 'asc']]);
        $orderArr = $this->Order->find('all', [
            'fields' => ['Order.id', 'Customer.nome_primario'],
            'contain' => ['Customer'],
            'order' => 'Order.id'
        ]);
        $orders = [];
        foreach ($orderArr as $order) {
            $orders[$order['Order']['id']] = $order['Order']['id'].' - '.$order['Customer']['nome_primario'];
        }

		$cancelarConta = $this->Permission->check(57, "escrita");

		$action = 'Contas a pagar';
		$breadcrumb = ['Alterar conta' => ''];
		$this->set("form_action", "edit");
		$this->set(compact('statuses', 'id', 'expenses', 'bankAccounts', 'costCenters', 'suppliers', 'planoContas', 'cancelarConta', 'resales', 'action', 'breadcrumb', 'orders'));

		$this->render("add");

	}

	public function delete($id){
		$this->Permission->check(15, "excluir") ? "" : $this->redirect("/not_allowed");
		$this->Outcome->id = $id;

		$data = ['Outcome' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

		if ($this->Outcome->save($data)) {
			$this->Flash->set(__('A conta a pagar foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect(array('action' => 'index'));
		}
	}

	public function change_status($id, $status){
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");
		$this->Outcome->id = $id;

		$data = ['Outcome' => ['status_id' => $status]];

		if ($this->Outcome->save($data)) {
			$this->Flash->set(__('Status alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect(array('action' => 'index/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')));
		}
	}

	public function reabrir_conta($id, $status)
	{
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->Outcome->id = $id;
		$log_old_value = $this->Outcome->read();

		$data = ['Outcome' => ['status_id' => $status, 'valor_pago' => null, 'data_pagamento' => null, 'usuario_id_pagamento' => null]];

		if ($this->Outcome->save($data)) {
			$new_value = $this->Outcome->read();

			$dados_log = [
				"old_value" => json_encode($log_old_value),
				"new_value" => json_encode($new_value),
				"route" => "outcomes/reabrir_conta",
				"log_action" => "reabrir_conta",
				"log_table" => "Outcome",
				"primary_key" => $id,
				"parent_log" => 0,
				"user_type" => "ADMIN",
				"user_id" => CakeSession::read("Auth.User.id"),
				"message" => "A conta a pagar foi alterada com sucesso",
				"log_date" => date("Y-m-d H:i:s"),
				"data_cancel" => "1901-01-01",
				"usuario_data_cancel" => 0,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];

			$this->Log->save($dados_log);

			$this->Flash->set(__('Status alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect(array('action' => 'index/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')));
		}
	}

	public function change_status_lote()
	{
    $this->autoRender = false;
    $this->layout = false;
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");

		$outcomeIds = $this->request->data['outcomeIds'];
		$status = $this->request->data['status'];

		$this->Outcome->updateAll(
        ['Outcome.status_id' => $status],
        ['Outcome.id' => $outcomeIds]
    );

    echo json_encode(['success' => true]);
	}

  public function avanca_lote() {
    $this->autoRender = false;
    $this->layout = false;
    $this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");

    $outcomeIds = $this->request->data['outcomeIds'];
    $status = $this->request->data['status'];
    $forma_de_pagamento = $this->request->data['forma_de_pagamento'];
    $valor_pago = isset($this->request->data['valor_pago']) ? str_replace(',', '.', str_replace('.', '', $this->request->data['valor_pago'])) : null;
    $data_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->data['data_pagamento'])));

    if ($status == 13) {
      if ($data_pagamento == '') {
        echo json_encode(['success' => false]);die;
      }

      foreach ($outcomeIds as $id) {
        $this->Outcome->recursive = -1;
        $this->Outcome->id = $id;
        $outcome = $this->Outcome->findById($id);

        $this->Outcome->save(['Outcome' => [
          'status_id' => 13,
          'valor_pago' => $valor_pago ?: $outcome['Outcome']['valor_total_not_formated'],
          'payment_method_baixa' => $forma_de_pagamento,
          'data_pagamento' => $data_pagamento,
          'usuario_id_pagamento' => CakeSession::read("Auth.User.id"),
          'updated' => date('Y-m-d H:i:s'),
          'user_updated_id' => CakeSession::read("Auth.User.id"),
        ]]);
      }
    } else {
      $this->Outcome->updateAll(
        [
          'Outcome.status_id' => $status,
          'updated' => "'".date('Y-m-d H:i:s')."'",
          'user_updated_id' => CakeSession::read("Auth.User.id"),
        ],
        ['Outcome.id' => $outcomeIds]
      );
    }

    echo json_encode(['success' => true]);
  }

	public function pagar_titulo($id){
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");
		$this->Outcome->id = $id;

		$valueFormatado = str_replace('.', '', $this->request->data['Outcome']['valor_pago']);
		$valueFormatado = str_replace(',', '.', $valueFormatado);
		$this->request->data['Outcome']['valor_pago'] = $valueFormatado;
		$this->request->data['Outcome']['data_pagamento'] = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->data['Outcome']['data_pagamento'])));
		$this->request->data['Outcome']['usuario_id_pagamento'] = CakeSession::read("Auth.User.id");

		if ($this->Outcome->save($this->request->data)) {
			$this->Flash->set(__('A conta a pagar foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect($this->referer());
		}
	}

	public function pagar_titulo_lote(){
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");

		$ids = explode(',', $this->request->data['Outcome']['ids']);

		foreach ($ids as $id) {
			$this->Outcome->recursive = -1;
			$this->Outcome->id = $id;
			$outcome = $this->Outcome->read();

			$this->request->data['Outcome']['valor_pago'] = $outcome['Outcome']['valor_total_not_formated'];
			$this->request->data['Outcome']['data_pagamento'] = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->data['Outcome']['data_pagamento'])));
			$this->request->data['Outcome']['usuario_id_pagamento'] = CakeSession::read("Auth.User.id");

			if (!$this->Outcome->save($this->request->data)) {
				$this->Flash->set(__('Houve algum erro!'), ['params' => ['class' => "alert alert-danger"]]);
				$this->redirect($this->referer());
			}
		}

		$this->Flash->set(__('A conta a pagar foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
		$this->redirect($this->referer());
	}

	 /*********************
                DOCUMENTOS
     **********************/
    public function documents($id)
    {
		    $this->Permission->check(15, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['Outcome.id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Docoutcome.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $this->Outcome->id = $id;
        $this->request->data = $this->Outcome->read();

        $action = 'Documentos';

       	$data = $this->Paginator->paginate('Docoutcome', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
       //debug($data);die;
        $this->set(compact('status', 'data', 'id', 'action'));
    }

	public function add_document($id)
{
    $this->Permission->check(15, 'escrita') ? '' : $this->redirect('/not_allowed');

    if ($this->request->is(['post', 'put'])) {
        $this->Docoutcome->create();

        if ($this->Docoutcome->validates()) {
            $this->request->data['Docoutcome']['user_creator_id'] = CakeSession::read('Auth.User.id');
            $this->request->data['Docoutcome']['status_id'] = 1;

            if (!empty($this->request->data['Docoutcome']['tipo_documento_id'])) {
                if ($this->Docoutcome->save($this->request->data)) {
                    $this->Flash->set(__('O documento foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    return $this->redirect(['action' => "../outcomes/documents/" . $id]);
                } else {
                    $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('Por favor, selecione o tipo do documento.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        } else {
            $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
        }
    }

    // Carrega os dados do Outcome
    $this->Outcome->id = $id;
    $cliente = $this->Outcome->read();

    $action = 'Documentos';

    // Busca os status
    $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

    // Carrega os tipos de documentos usando a coluna 'nome'
    $this->loadModel('TipoDocumento');
    $tiposDocumentos = $this->TipoDocumento->find('list', [
        'fields' => ['TipoDocumento.id', 'TipoDocumento.nome']
    ]);

    // Define as variáveis para a view
    $this->set("form_action", "../outcomes/add_document/" . $id);
    $this->set(compact('statuses', 'tiposDocumentos', 'action', 'id'));
}


public function edit_document($id, $document_id = null)
{
    $this->Permission->check(15, 'escrita') ? '' : $this->redirect('/not_allowed');

    // Define o ID do documento
    $this->Docoutcome->id = $document_id;

    // Verifica se a requisição é do tipo post ou put
    if ($this->request->is(['post', 'put'])) {
        $this->Docoutcome->validates();

        // Se o campo de arquivo estiver vazio, remove-o dos dados
        if (empty($this->request->data['Docoutcome']['file']['name'])) {
            unset($this->request->data['Docoutcome']['file']);
        }

        // Define o usuário que está atualizando o documento
        $this->request->data['Docoutcome']['user_updated_id'] = CakeSession::read('Auth.User.id');

        // Salva as alterações no documento
        if ($this->Docoutcome->save($this->request->data)) {
            $this->Flash->set(__('O documento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            return $this->redirect(['action' => 'documents/' . $id]);
        } else {
            $this->Flash->set(__('O documento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
        }
    }

    // Carrega os erros de validação temporários
    $temp_errors = $this->Docoutcome->validationErrors;

    // Carrega os dados do documento para o formulário de edição
    $this->request->data = $this->Docoutcome->read();

    // Define os erros de validação de volta
    $this->Docoutcome->validationErrors = $temp_errors;

    // Carrega os status
    $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

    // Carrega os tipos de documentos
    $this->loadModel('TipoDocumento');
    $tiposDocumentos = $this->TipoDocumento->find('list', [
        'fields' => ['TipoDocumento.id', 'TipoDocumento.nome']
    ]);

    // Define as variáveis para a view
    $this->set("action", 'Documentos');
    $this->set("form_action", "../outcomes/edit_document/" . $id . '/' . $document_id);
    $this->set(compact('statuses', 'tiposDocumentos', 'id', 'document_id'));

    // Renderiza a mesma view utilizada para adicionar
    $this->render("add_document");
}


	public function delete_document($outcome_id, $id)
    {
        $this->Permission->check(15, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->Docoutcome->id = $id;
        $this->request->data = $this->Docoutcome->read();

        $this->request->data['Docoutcome']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['Docoutcome']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->Docoutcome->save($this->request->data)) {
            unlink(APP . 'webroot/files/docoutcome/file/' . $this->request->data["Docoutcome"]["id"] . '/' . $this->request->data["Docoutcome"]["file"]);

            $this->Flash->set(__('O documento foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'documents/' . $outcome_id]);
        }
    }

	public function download_zip_document()
    {
        $this->autoRender = false;
        $this->layout = false;

		$outcomeIds = $this->request->data['outcomeIds'];
		$status = $this->request->data['status'];
		$arq_file = false;

		if ($status == 11) {
			$zip_name = "arquivos_programado_" . date('d_m_Y_H_i_s') . ".zip";
		} elseif ($status == 12) {
			$zip_name = "arquivos_aprovado_" . date('d_m_Y_H_i_s') . ".zip";
		} elseif ($status == 13) {
			$zip_name = "arquivos_pago_" . date('d_m_Y_H_i_s') . ".zip";
		} else {
			$zip_name = "arquivos_pendente_" . date('d_m_Y_H_i_s') . ".zip";
		}

	    $zip_file = APP."webroot/files/docoutcome/file/".$zip_name;

	    if (file_exists($zip_file)) {
	    	unlink($zip_file);
	    }

	    $zip = new ZipArchive();
	    $zip->open($zip_file, ZIPARCHIVE::CREATE);

		$docs = $this->Docoutcome->find('all', ['conditions' => ['Docoutcome.outcome_id' => $outcomeIds], 'fields' => ['Docoutcome.id', 'Docoutcome.outcome_id', 'Docoutcome.file'] ]);

		foreach ($docs as $doc) {
	        $caminho  = APP."webroot/files/docoutcome/file/".$doc["Docoutcome"]["id"]."/";

	        if (file_exists($caminho)) {
		        $files = scandir($caminho);
		        $f = $doc["Docoutcome"]["file"];

		        foreach ($files as $key => $arq) {
		            if($arq != '.' && $arq != '..'){
		                if (strpos($arq, $f) !== false) {
		                	$arq_file = true;
		                    $zip->addFile($caminho.$arq, $doc["Docoutcome"]["id"]."_".$arq);
		                }
		            }
		        }
	        }
		}

		if ($arq_file) {
        	return json_encode(['success' => true, 'nome_zip' => $zip_name]);
		} else {
			return json_encode(['success' => false]);
		}
    }

	public function download_zip_document_id()
    {
        $this->autoRender = false;
        $this->layout = false;

		$docOutcomeIds = $this->request->data['docOutcomeIds'];
		$arq_file = false;

		$zip_name = "documentos_" . date('d_m_Y_H_i_s') . ".zip";
	    $zip_file = APP."webroot/files/docoutcome/file/".$zip_name;

	    if (file_exists($zip_file)) {
	    	unlink($zip_file);
	    }

	    $zip = new ZipArchive();
	    $zip->open($zip_file, ZIPARCHIVE::CREATE);

		$docs = $this->Docoutcome->find('all', ['conditions' => ['Docoutcome.id' => $docOutcomeIds], 'fields' => ['Docoutcome.id', 'Docoutcome.outcome_id', 'Docoutcome.file'] ]);

		foreach ($docs as $doc) {
	        $caminho  = APP."webroot/files/docoutcome/file/".$doc["Docoutcome"]["id"]."/";

	        if (file_exists($caminho)) {
		        $files = scandir($caminho);
		        $f = $doc["Docoutcome"]["file"];

		        foreach ($files as $key => $arq) {
		            if($arq != '.' && $arq != '..'){
		                if (strpos($arq, $f) !== false) {
		                	$arq_file = true;
		                    $zip->addFile($caminho.$arq, $doc["Docoutcome"]["id"]."_".$arq);
		                }
		            }
		        }
	        }
		}

		if ($arq_file) {
        	return json_encode(['success' => true, 'nome_zip' => $zip_name]);
		} else {
			return json_encode(['success' => false]);
		}
    }

	public function all_documents()
	{
		$this->Permission->check(15, 'leitura') ? '' : $this->redirect('/not_allowed');

		$this->Paginator->settings = [
			'Docoutcome' => [
				'limit' => 50,
				'order' => [
					'Outcome.id' => 'asc',
					'Docoutcome.created' => 'asc'
				],
				'joins' => [
					[
						'table' => 'suppliers',
						'alias' => 'Supplier',
						'type' => 'LEFT',
						'conditions' => ['Outcome.supplier_id = Supplier.id']
					],
					[
						'table' => 'statuses',
						'alias' => 'OutcomeStatus',
						'type' => 'LEFT',
						'conditions' => ['Outcome.status_id = OutcomeStatus.id']
					]
				],
				'fields' => [
					'Docoutcome.*',
					'Outcome.*',
					'TipoDocumento.*',
					'Supplier.nome_fantasia',
					'Status.*',
					'OutcomeStatus.*',
					"(SELECT c.nome_primario
						FROM orders o 
							INNER JOIN customers c ON o.customer_id = c.id  
						WHERE o.id = Outcome.order_id  
								AND c.data_cancel = '1901-01-01 00:00:00' 
								AND o.data_cancel = '1901-01-01 00:00:00' 
					) as nome_primario"
				]
			]
		];

		$condition = ['and' => [], 'or' => []];

		// Filtro de busca
		if (isset($_GET['q']) && $_GET['q'] != "") {
			$condition['or'] = array_merge($condition['or'], ['Docoutcome.name LIKE' => "%" . $_GET['q'] . "%"]);
		}

	    // Filtro de Status
        if (isset($_GET['t']) && $_GET['t'] != '') {
            $statusId = $_GET['t'];

            // Verifica se o filtro de 't' corresponde ao status em 'outcomes'
            $condition['and'] = array_merge(
                $condition['and'],
                ['OR' => [
                    'Status.id' => $statusId,
                    'Outcome.status_id' => $statusId // Adiciona o filtro para outcomes
                ]]
            );
        }

		// Filtro de vencimento
		if (isset($_GET['vencimento_de']) && $_GET['vencimento_de'] != '') {
			$condition['and'][] = ['Outcome.vencimento >=' => $_GET['vencimento_de']];
		}

		if (isset($_GET['vencimento_ate']) && $_GET['vencimento_ate'] != '') {
			$condition['and'][] = ['Outcome.vencimento <=' => $_GET['vencimento_ate']];
		}

		// Filtro de data de pagamento
		if (isset($_GET['data_pagamento_de']) && $_GET['data_pagamento_de'] != '') {
			$condition['and'][] = ['Outcome.data_pagamento >=' => $_GET['data_pagamento_de']];
		}

		if (isset($_GET['data_pagamento_ate']) && $_GET['data_pagamento_ate'] != '') {
			$condition['and'][] = ['Outcome.data_pagamento <=' => $_GET['data_pagamento_ate']];
		}

		// Filtro por Tipo de Documento
		if (isset($_GET['tipo_documento']) && $_GET['tipo_documento'] != '') {
			$condition['and'][] = ['TipoDocumento.id' => $_GET['tipo_documento']];
		}

		$action = 'Documentos';

		$data = $this->Paginator->paginate('Docoutcome', $condition);
		$status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 4]]);
		$tiposDocumentos = $this->TipoDocumento->find('all'); // Busca os tipos de documento

		$this->set(compact('status', 'tiposDocumentos', 'data', 'action'));
	}
	
	public function payments($id) {
        $this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Outcome->id = $id;
        $this->request->data = $this->Outcome->read();
        $order_id = $this->request->data['Outcome']['order_id'];
        $order = $this->Order->find('first', ['conditions' => ['Order.id' => $order_id]]);

        $this->Paginator->settings = ['OrderItem' => [
            'limit' => 100,
            'order' => ['CustomerUser.name' => 'asc'],
            'fields' => ['OrderItem.*', 'Benefit.*', 'Order.*', 'CustomerUser.*', 'PixStatus.*', 'CustomerUserBankAccounts.*', 'Outcome.*', 'BankCode.*'],
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
                    'table' => 'customer_user_bank_accounts',
                    'alias' => 'CustomerUserBankAccounts',
                    'type' => 'LEFT',
                    'conditions' => [
                        'CustomerUserBankAccounts.customer_user_id = CustomerUser.id'
                    ]
                ],
                [
                    'table' => 'bank_codes',
                    'alias' => 'BankCode',
                    'type' => 'LEFT',
                    'conditions' => [
                        'CustomerUserBankAccounts.bank_code_id = BankCode.id'
                    ]
                ],
                [
                    'table' => 'outcomes',
                    'alias' => 'Outcome',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Outcome.order_id = Order.id'
                    ]
                ]
            ]
        ]];

        $condition = ["and" => ['Order.id' => $order_id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['CustomerUser.name LIKE' => "%" . $_GET['q'] . "%", 'CustomerUser.cpf LIKE' => "%" . $_GET['q'] . "%", 'Benefit.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        $items = $this->Paginator->paginate('OrderItem', $condition);

        $outcome = $this->Outcome->read(null, $id);
        $pendingPix = $this->CustomerUser->find_pix_pendentes($outcome['Outcome']['order_id']);

        $action = 'Contas a pagar';
        $breadcrumb = ['Pagamentos' => ''];
        $this->set(compact('breadcrumb', 'action', 'id', 'order', 'items', 'pendingPix'));
    }

    public function enviar_btg($id) {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $outcome = $this->Outcome->read(null, $id);
        $pix_pendentes = $this->CustomerUser->find_pix_pendentes($outcome['Outcome']['order_id']);

        if (!count($pix_pendentes)) {
            $this->Flash->set(__('Não há pagamentos pendentes.'), ['params' => ['class' => "alert alert-warning"]]);
            $this->redirect(['action'=> 'payments', $id]);
        } elseif (!$outcome['Outcome']['data_pagamento']) {
            $this->Flash->set(__('Cadastre uma data de pagamento para realizar a liberação do pix.'), ['params' => ['class' => "alert alert-warning"]]);
            $this->redirect(['action'=> 'payments', $id]);
        }

        $hasError = false;
        for ($i=0; $i < count($pix_pendentes); $i++) {
            $orderItem = $pix_pendentes[$i];

            $ApiBtgPactual = new ApiBtgPactual();
            $success = $ApiBtgPactual->criaPagamentoPix($orderItem, $outcome['Outcome']['data_pagamento_nao_formatado']);

            if (!$success) {
                $hasError = true;
            }

            $pix_status_id = $success ? 111 : 110;
            $this->OrderItem->id = $orderItem['i']['id'];
            $this->OrderItem->save([
                'pix_status_id' => $pix_status_id,
                'bank_sent_date' => date('Y-m-d H:i:s')
            ]);
            $this->OrderItem->clear();
        }
        if ($hasError) {
            $this->Flash->set(__('Não foi possível enviar todos os pedidos. Por favor, verifique os dados e tente novamente.'), ['params' => ['class' => "alert alert-warning"]]);
        } else {
            $this->Flash->set(__('Itens enviados com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
        }
        $this->redirect(['action'=> 'payments', $id]);
    }

    public function marcar_pix_pago($id) {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $this->OrderItem->id = $id;
        $this->OrderItem->save([
            'pix_status_id' => 112,
        ]);

        $this->redirect($this->referer());
    }
}
