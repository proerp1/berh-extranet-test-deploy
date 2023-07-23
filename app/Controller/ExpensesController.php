<?php
class ExpensesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Expense', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Expense.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(14, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Expense.name LIKE' => "%".$_GET['q']."%", 'Expense.description LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Expense', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Despesas';
        $breadcrumb = ['Cadastros' => '', 'Despesas' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(14, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Expense->create();
            if ($this->Expense->validates()) {
                $this->request->data['Expense']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Expense->save($this->request->data)) {
                    $this->Flash->set(__('A despesa foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->set(__('A despesa não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A despesa não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Despesas';
        $breadcrumb = ['Cadastros' => '', 'Despesas' => '', 'Nova despesa' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(14, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Expense->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Expense->validates();
            $this->request->data['Expense']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Expense->save($this->request->data)) {
                $this->Flash->set(__('A despesa foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('A despesa não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Expense->validationErrors;
        $this->request->data = $this->Expense->read();
        $this->Expense->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Despesas';
        $breadcrumb = ['Cadastros' => '', 'Despesas' => '', 'Alterar despesa' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(14, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Expense->id = $id;
        $this->request->data = $this->Expense->read();

        $this->request->data['Expense']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Expense']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Expense->save($this->request->data)) {
            $this->Flash->set(__('A despesa foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
