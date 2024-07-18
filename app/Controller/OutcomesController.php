<?php
class OutcomesController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission', 'ExcelGenerator'];
	public $uses = ['Outcome', 'Status', 'Expense', 'BankAccount', 'CostCenter', 'Supplier', 'Log', 'PlanoConta', 'Resale', 'Docoutcome', 'Order'];

	public $paginate = [
		'limit' => 175, 'order' => ['Outcome.vencimento' => 'asc', 'Status.id' => 'asc', 'Outcome.name' => 'asc', 'Outcome.doc_num' => 'asc']
	];

	public function beforeFilter() { 
		parent::beforeFilter(); 
	}

	public function index() {
		$this->Permission->check(15, "leitura") ? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Outcome.resale_id' => CakeSession::read("Auth.User.resales")], "or" => []];
		

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%", 'Outcome.doc_num LIKE' => "%".$_GET['q']."%", 'Outcome.name LIKE' => "%".$_GET['q']."%", 'BankAccount.name LIKE' => "%".$_GET['q']."%"]);
		}
		
		if(isset($_GET["t"]) and $_GET["t"] != ""){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
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

		if (isset($_GET['exportar'])) {
			$nome = 'contas_pagar.xlsx';

			$data = $this->Outcome->find('all', ['conditions' => $condition]);

			$this->ExcelGenerator->gerarExcelOutcome($nome, $data);

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
				
				$aba_pago_id = 13;
				$aba_atual_id = isset($_GET['t']) ? $_GET['t'] : null;
				$exibir_segundo_card = $aba_atual_id == $aba_pago_id;

				

		$this->Paginator->settings['order'] = ['Outcome.created' => 'DESC'];
		$data = $this->Paginator->paginate('Outcome', $condition);
		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 4)));

		$action = 'Contas a pagar';
		$this->set(compact('status', 'data', 'action', 'total_outcome', 'pago_outcome', 'exibir_segundo_card', 'aba_atual_id', 'aba_pago_id'));
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
		$bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => 'BankAccount.name']);
		$costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1], 'order' => 'CostCenter.name']);
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

				$this->Session->setFlash(__('A conta a pagar foi alterada com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect(array('action' => 'index/?'.$this->request->data['query_string']));
			} else {
				$this->Session->setFlash(__('A conta a pagar não pode ser alterada, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$temp_errors = $this->Outcome->validationErrors;
		$this->request->data = $this->Outcome->read();
		$this->Outcome->validationErrors = $temp_errors;
		
		$statuses = $this->Status->find('list', array('conditions' => array('Status.categoria' => 4)));
		$expenses = $this->Expense->find('list', ['conditions' => ['Expense.status_id' => 1], 'order' => 'Expense.name']);
		$bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => 'BankAccount.name']);
		$costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1], 'order' => 'CostCenter.name']);
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
		$this->set(compact('statuses', 'id', 'expenses', 'bankAccounts', 'costCenters', 'suppliers', 'planoContas', 'cancelarConta', 'resales', 'action', 'breadcrumb', 'order'));
		
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
		$this->Permission->check(11, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['Outcome.id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Docoutcome.name LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $this->Outcome->id = $id;
        $cliente = $this->Outcome->read();

        $action = 'Documentos';

       	$data = $this->Paginator->paginate('Docoutcome', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
       
        $this->set(compact('status', 'data', 'id', 'action'));
    }
	public function add_document($id)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is(['post', 'put'])) {
            $this->Docoutcome->create();
            if ($this->Docoutcome->validates()) {
                $this->request->data['Docoutcome']['user_creator_id'] = CakeSession::read('Auth.User.id');
                if ($this->Docoutcome->save($this->request->data)) {
                    $this->Flash->set(__('O documento foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "../outcomes/documents/" . $id]);
                } else {
                    $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
                }
            } else {
                $this->Flash->set(__('O documento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
            
        }

        $this->Outcome->id = $id;
        $cliente = $this->Outcome->read();

        $action = 'Documentos';

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
       
        $this->set("form_action", "../outcomes/add_document/" . $id);
        $this->set(compact('statuses', 'action', 'id'));
    }
	
	public function edit_document($id, $document_id = null)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Docoutcome->id = $document_id;
        if ($this->request->is(['post', 'put'])) {
            $this->Docoutcome->validates();
            if ($this->request->data['Docoutcome']['file']['name'] == '') {
                unset($this->request->data['Docoutcome']['file']);
            }
            $this->request->data['Docoutcome']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->Docoutcome->save($this->request->data)) {
                $this->Flash->set(__('O documento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'documents/' . $id]);
            } else {
                $this->Flash->set(__('O documento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->Docoutcome->validationErrors;
        $this->request->data = $this->Docoutcome->read();
        $this->Docoutcome->validationErrors = $temp_errors;

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
       
        $this->set("action", 'Documentos');
        $this->set("form_action", "../outcomes/edit_document/" . $id);
        $this->set(compact('statuses', 'id', 'document_id'));

        $this->render("add_document");
    }

	public function delete_document($outcome_id, $id)
    {
        $this->Permission->check(11, 'excluir') ? '' : $this->redirect('/not_allowed');
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
    

}
