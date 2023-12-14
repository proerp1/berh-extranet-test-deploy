<?php
class SuppliersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Supplier', 'Status','BankCode','BankAccountType'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Supplier.id' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(9, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Supplier.id LIKE' => "%".$_GET['q']."%",'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%", 'Supplier.razao_social LIKE' => "%".$_GET['q']."%", 'Supplier.documento LIKE' => "%".$_GET['q']."%"]);
        }
        

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Supplier', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(9, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Supplier->create();
            if ($this->Supplier->validates()) {
                $this->request->data['Supplier']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Supplier->save($this->request->data)) {
                    $this->Flash->set(__('O fornecedor foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/'.$this->Supplier->id]);
                } else {
                    $this->Flash->set(__('O fornecedor não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O fornecedor não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $banks = $this->BankCode->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Novo fornecedor' => ''];
        $this->set(compact('statuses', 'action', 'breadcrumb', 'banks', 'bank_account_type'));
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(9, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Supplier->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Supplier->validates();
            $this->request->data['Supplier']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Supplier->save($this->request->data)) {
                $this->Flash->set(__('O fornecedor foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('O fornecedor não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Supplier->validationErrors;
        $this->request->data = $this->Supplier->read();
        $this->Supplier->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $banks = $this->BankCode->find('list');
        $bank_account_type = $this->BankAccountType->find('list', ['fields' => ['id', 'description']]);
        $action = 'Fornecedores';
        $breadcrumb = ['Cadastros' => '', 'Fornecedores' => '', 'Alterar fornecedor' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb','banks','bank_account_type'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(9, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Supplier->id = $id;
        $this->request->data = $this->Supplier->read();

        $this->request->data['Supplier']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Supplier']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Supplier->save($this->request->data)) {
            $this->Flash->set(__('O fornecedor foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
