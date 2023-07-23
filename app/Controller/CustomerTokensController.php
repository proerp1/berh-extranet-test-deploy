<?php
class CustomerTokensController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission','Email'];
	public $uses = ['Customer', 'CustomerToken', 'Status', 'Log'];

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
	}

	public function index($id) {
		$this->Permission->check(3, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Customer.id' => $id], "or" => []];

		if(!empty($_GET['q'])){
				$condition['or'] = array_merge($condition['or'], ['CustomerToken.token LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['CustomerToken.status_id' => $_GET['t']]);
		}

		$this->Customer->id = $id;
		$cliente = $this->Customer->read();

		$action = 'Tokens';

		$data = $this->Paginator->paginate('CustomerToken', $condition);

		$checkStatus = $this->CustomerToken->find('count', ['conditions' => ['CustomerToken.status_id' => 1, 'CustomerToken.customer_id' => $id]]);
		$status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
		$breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Tokens' => ''
        ];
		$this->set(compact('status', 'data', 'action', 'id', 'checkStatus', 'breadcrumb'));
	}

	public function add($id) {
		$this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");

		if ($this->request->is(['post', 'put'])) {
			$this->request->data['CustomerToken']['user_creator_id'] = CakeSession::read("Auth.User.id");
			$this->request->data['CustomerToken']['token'] = AuthComponent::password(uniqid());

			$this->CustomerToken->create();
			if ($this->CustomerToken->save($this->request->data)) {
				$this->Flash->set(__('O token foi salvo com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect(array('action' => 'index', $id));
			} else {
				$this->Flash->set(__('O token não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$this->Customer->id = $id;
		$cliente = $this->Customer->read();

		$statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

		$action = 'Tokens';
		$breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Novo token' => ''
        ];
		$this->set("form_action", "../customer_tokens/add/".$id);
		$this->set(compact('statuses', 'action', 'id', 'breadcrumb'));
	}

	public function edit($id, $token_id = null) {
		$this->Permission->check(3, "escrita") ? "" : $this->redirect("/not_allowed");
		$this->CustomerToken->id = $token_id;
		if ($this->request->is(['post', 'put'])) {
			$this->request->data['CustomerToken']['user_updated_id'] = CakeSession::read("Auth.User.id");

			$log_old_value = $this->request->data["log_old_value"];
			unset($this->request->data["log_old_value"]);
			
			$dados_log = [
				"old_value" => $log_old_value,
				"new_value" => json_encode($this->request->data),
				"route" => "customer_tokens/edit",
				"log_action" => "Alterou",
				"log_table" => "CustomerToken",
				"primary_key" => $token_id,
				"parent_log" => $id,
				"user_type" => "ADMIN",
				"user_id" => CakeSession::read("Auth.User.id"),
				"message" => "O token foi alterado com sucesso",
				"log_date" => date("Y-m-d H:i:s"),
				"data_cancel" => "1901-01-01",
				"usuario_data_cancel" => 0,
				"ip" => $_SERVER["REMOTE_ADDR"]
			];
			if ($this->CustomerToken->save($this->request->data)) {
				$this->Log->save($dados_log);
				$this->Flash->set(__('O token foi alterado com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect(array('action' => 'index', $id));
			} else {
				$this->Flash->set(__('O token não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}

		$temp_errors = $this->CustomerToken->validationErrors;
		$this->request->data = $this->CustomerToken->read();
		$this->CustomerToken->validationErrors = $temp_errors;
		
		$this->Customer->id = $id;
        $this->Customer->recursive = -1;
		$cliente = $this->Customer->read();

		$action = 'Tokens';

		$statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
	
		$breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Alterar token' => ''
        ];
		$this->set("form_action", "../customer_tokens/edit/".$id);
		$this->set(compact('statuses', 'action', 'id', 'token_id', 'breadcrumb'));
		
		$this->render("add");
	}

	public function delete($customer_id, $id){
		$this->Permission->check(3, "excluir") ? "" : $this->redirect("/not_allowed");
		$this->CustomerToken->id = $id;

		$data = ['CustomerToken' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

		if ($this->CustomerToken->save($data)) {
			$this->Flash->set(__('O token foi excluido com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect(array('action' => 'index', $customer_id));
		}
	}
}
