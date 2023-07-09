<?php
class BankAccountsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['BankAccount', 'Status', 'Bank'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'BankAccount.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(12, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['BankAccount.name LIKE' => "%".$_GET['q']."%", 'BankAccount.agency LIKE' => "%".$_GET['q']."%", 'BankAccount.account_number LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('BankAccount', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Contas e Boletos';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Contas e Boletos' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(12, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->BankAccount->create();
            if ($this->BankAccount->validates()) {
                $this->request->data['BankAccount']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->BankAccount->save($this->request->data)) {
                    $this->Session->setFlash(__('A conta bancária foi salva com sucesso'), 'default', ['class' => "alert alert-success"]);
                    $this->redirect(['action' => 'edit/'.$this->BankAccount->id]);
                } else {
                    $this->Session->setFlash(__('A conta bancária não pode ser salva, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
                }
            } else {
                $this->Session->setFlash(__('A conta bancária não pode ser salva, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $banks = $this->Bank->find('list');

        $action = 'Contas e Boletos';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Contas e Boletos' => '', 'Nova conta bancária' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'banks', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(12, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->BankAccount->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->BankAccount->validates();
            $this->request->data['BankAccount']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->BankAccount->save($this->request->data)) {
                $this->Session->setFlash(__('A conta bancária foi alterada com sucesso'), 'default', ['class' => "alert alert-success"]);
            } else {
                $this->Session->setFlash(__('A conta bancária não pode ser alterada, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->BankAccount->validationErrors;
        $this->request->data = $this->BankAccount->read();
        $this->BankAccount->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $banks = $this->Bank->find('list');

        $action = 'Contas e Boletos';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Contas e Boletos' => '', $this->request->data['BankAccount']['name'] => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'banks', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(12, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->BankAccount->id = $id;
        $this->request->data = $this->BankAccount->read();

        $this->request->data['BankAccount']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['BankAccount']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->BankAccount->save($this->request->data)) {
            $this->Session->setFlash(__('A conta bancária foi excluida com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        }
    }
}
