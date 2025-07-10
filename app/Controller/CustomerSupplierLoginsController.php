<?php
class CustomerSupplierLoginsController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission','Email'];
	public $uses = ['Customer', 'Supplier', 'CustomerSupplierLogin', 'Log', 'Status', 'EconomicGroup'];

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
	}

	public function index($tipo, $id) {
		$this->Permission->check(77, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		if ($tipo == 1) {
			$condition = ["and" => ['Customer.id' => $id], "or" => []];

			$this->Customer->id = $id;
			$cliente = $this->Customer->read();

			$breadcrumb = [
	            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
	            'Logins' => ''
	        ];
		} else {
			$condition = ["and" => ['Supplier.id' => $id], "or" => []];

			$this->Supplier->id = $id;
			$supplier = $this->Supplier->read();

        	$breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Logins' => ''];
		}		

		if(!empty($_GET['q'])){
			$condition['or'] = array_merge($condition['or'], ['CustomerSupplierLogin.login LIKE' => "%".$_GET['q']."%"]);
		}

		$action = 'Logins e Senhas';
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

		$data = $this->Paginator->paginate('CustomerSupplierLogin', $condition);
		$this->set(compact('statuses','data', 'action', 'id', 'breadcrumb', 'tipo'));
	}

	public function add($tipo, $id) {
		$this->Permission->check(77, "escrita") ? "" : $this->redirect("/not_allowed");

		if ($this->request->is(['post', 'put'])) {
			$this->request->data['CustomerSupplierLogin']['user_created_id'] = CakeSession::read("Auth.User.id");

			$this->CustomerSupplierLogin->create();
			if ($this->CustomerSupplierLogin->save($this->request->data)) {
				$this->Flash->set(__('O login foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
				$this->redirect(array('action' => 'index', $tipo, $id));
			} else {
				$this->Flash->set(__('O login não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}	

		$action = 'Logins e Senhas';

		if ($tipo == 1) {
			$customers = false;

			$suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia'], 'order' => 'Supplier.nome_fantasia']);

			$economicGroups = $this->EconomicGroup->find("list", ["conditions" => ["EconomicGroup.status_id" => 1, 'EconomicGroup.customer_id' => $id]]);

			$this->Customer->id = $id;
			$cliente = $this->Customer->read();

			$breadcrumb = [
	            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
	            'Novo Login' => ''
	        ];
		} else {
			$suppliers = false;

			$customers = $this->Customer->find('list', ['order' => ['Customer.nome_primario']]);

			$economicGroups = false;

			$this->Supplier->id = $id;
			$supplier = $this->Supplier->read();

        	$breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Logins' => '', 'Novo Login' => ''];
		}
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

		$this->set("form_action", "../customer_supplier_logins/add/".$tipo."/".$id);
		$this->set(compact('statuses','action', 'id', 'breadcrumb', 'suppliers', 'customers', 'tipo', 'economicGroups'));
	}

	public function edit($tipo, $id, $cust_supp_id = null) {
		$this->Permission->check(77, "leitura") ? "" : $this->redirect("/not_allowed");

		$this->CustomerSupplierLogin->id = $cust_supp_id;

		if ($this->request->is(['post', 'put'])) {
			
			$this->Permission->check(77, "escrita") ? "" : $this->redirect("/not_allowed");
			$this->request->data['CustomerSupplierLogin']['user_updated_id'] = CakeSession::read("Auth.User.id");

			$log_old_value = $this->request->data["log_old_value"];
			unset($this->request->data["log_old_value"]);
			
			$dados_log = [
				"old_value" => $log_old_value,
				"new_value" => json_encode($this->request->data),
				"route" => "customer_supplier_logins/edit",
				"log_action" => "Alterou",
				"log_table" => "CustomerSupplierLogin",
				"primary_key" => $cust_supp_id,
				"parent_log" => $id,
				"user_type" => "ADMIN",
				"user_id" => CakeSession::read("Auth.User.id"),
				"message" => "O login foi alterado com sucesso",
				"log_date" => date("Y-m-d H:i:s"),
				"data_cancel" => "1901-01-01",
				"usuario_data_cancel" => 0,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			if ($this->CustomerSupplierLogin->save($this->request->data)) {
				$this->Log->save($dados_log);
				$this->Flash->set(__('O login foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
				$this->redirect(array('action' => 'index', $tipo, $id));
			} else {
				$this->Flash->set(__('O login não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}

		$temp_errors = $this->CustomerSupplierLogin->validationErrors;
		$this->request->data = $this->CustomerSupplierLogin->read();
		$this->CustomerSupplierLogin->validationErrors = $temp_errors;

		$action = 'Logins e Senhas';

		if ($tipo == 1) {
			$customers = false;

			$suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia'], 'order' => 'Supplier.nome_fantasia']);

			$economicGroups = $this->EconomicGroup->find("list", ["conditions" => ["EconomicGroup.status_id" => 1, 'EconomicGroup.customer_id' => $id]]);

			$this->Customer->id = $id;
			$cliente = $this->Customer->read();

			$breadcrumb = [
	            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
	            'Alterar Login' => ''
	        ];
		} else {
			$suppliers = false;

			$customers = $this->Customer->find('list', ['order' => ['Customer.nome_primario']]);

			$economicGroups = false;

			$this->Supplier->id = $id;
			$supplier = $this->Supplier->read();

        	$breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Logins' => '', 'Novo Login' => ''];
		}
		
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

		$this->set("form_action", "../customer_supplier_logins/edit/".$tipo."/".$id);
		$this->set(compact('statuses','action', 'id', 'cust_supp_id', 'breadcrumb', 'suppliers', 'customers', 'tipo', 'economicGroups'));
		
		$this->render("add");
	}

	public function delete($tipo, $id, $cust_supp_id){
		$this->Permission->check(77, "excluir") ? "" : $this->redirect("/not_allowed");
		$this->CustomerSupplierLogin->id = $cust_supp_id;

		$data = ['CustomerSupplierLogin' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

		if ($this->CustomerSupplierLogin->save($data)) {
			$this->Flash->set(__('O login foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect(array('action' => 'index', $tipo, $id));
		}
	}
}
