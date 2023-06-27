<?php
class OutcomesController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission', 'ExcelGenerator'];
	public $uses = ['Outcome', 'Status', 'Expense', 'BankAccount', 'CostCenter', 'Supplier', 'Log', 'PlanoConta', 'Resale'];

	public $paginate = [
		'limit' => 10, 'order' => ['Status.id' => 'asc', 'Outcome.name' => 'asc']
	];

	public function beforeFilter() { 
		parent::beforeFilter(); 
	}

	public function index() {
		$this->Permission->check(15, "leitura") ? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Outcome.resale_id' => CakeSession::read("Auth.User.resales")], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Outcome.name LIKE' => "%".$_GET['q']."%", 'BankAccount.name LIKE' => "%".$_GET['q']."%"]);
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

		if (isset($_GET['exportar'])) {
			$nome = 'contas_pagar.xlsx';

			$data = $this->Outcome->find('all', ['conditions' => $condition]);

			$this->ExcelGenerator->gerarExcelOutcome($nome, $data);

			$this->redirect("/files/excel/".$nome);
		}

		$data = $this->Paginator->paginate('Outcome', $condition);
		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 4)));

		$action = 'Contas a pagar';
		$this->set(compact('status', 'data', 'action'));
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
					$this->redirect(array('action' => 'index/?'.$this->request->data['query_string']));
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

		$action = 'Contas a pagar';
		$breadcrumb = ['Nova conta' => ''];
		$this->set("form_action", "add");
		$this->set(compact('statuses', 'expenses', 'bankAccounts', 'costCenters', 'suppliers', 'planoContas', 'cancelarConta', 'resales', 'action', 'breadcrumb'));
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

		$cancelarConta = $this->Permission->check(57, "escrita");

		$action = 'Contas a pagar';
		$breadcrumb = ['Alterar conta' => ''];
		$this->set("form_action", "edit");
		$this->set(compact('statuses', 'id', 'expenses', 'bankAccounts', 'costCenters', 'suppliers', 'planoContas', 'cancelarConta', 'resales', 'action', 'breadcrumb'));
		
		$this->render("add");
	}

	public function delete($id){
		$this->Permission->check(15, "excluir") ? "" : $this->redirect("/not_allowed");
		$this->Outcome->id = $id;
		
		$data = ['Outcome' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

		if ($this->Outcome->save($data)) {
			$this->Session->setFlash(__('A conta a pagar foi excluida com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect(array('action' => 'index'));
		}
	}

	public function change_status($id, $status){
		$this->Permission->check(15, "escrita") ? "" : $this->redirect("/not_allowed");
		$this->Outcome->id = $id;

		$data = ['Outcome' => ['status_id' => $status]];

		if ($this->Outcome->save($data)) {
			$this->Session->setFlash(__('Status alterado com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect(array('action' => 'index/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')));
		}
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
			$this->Session->setFlash(__('A conta a pagar foi salva com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect($this->referer());
		}
	}
}