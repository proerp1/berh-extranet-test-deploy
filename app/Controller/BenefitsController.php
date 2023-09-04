<?php
class BenefitsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Benefit', 'Status', 'Supplier', 'BenefitType', 'CepbrEstado'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Benefit.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(16, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Benefit.name LIKE' => "%".$_GET['q']."%", 'Supplier.nome_fantasia LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('Benefit', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Benefit->create();
            if ($this->Benefit->validates()) {
                $this->request->data['Benefit']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Benefit->save($this->request->data)) {
                    $this->Flash->set(__('O Benefício foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->set(__('O Benefício não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O Benefício não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        // $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia'], 'order' => 'Supplier.nome_fantasia']]);
        $benefit_types = $this->BenefitType->find('list');
        $states = $this->CepbrEstado->find('list');

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Novo Benefício' => ''];
        $this->set("form_action", "add");
        $this->set(compact('action', 'breadcrumb', 'suppliers', 'benefit_types', 'states'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Benefit->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Benefit->validates();
            $this->request->data['Benefit']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Benefit->save($this->request->data)) {
                $this->Flash->set(__('O Benefício foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O Benefício não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Benefit->validationErrors;
        $this->request->data = $this->Benefit->read();
        $this->Benefit->validationErrors = $temp_errors;
        
        $suppliers = $this->Supplier->find('list', ['fields' => ['id', 'nome_fantasia']]);
        $benefit_types = $this->BenefitType->find('list');
        $states = $this->CepbrEstado->find('list');

        $action = 'Benefício';
        $breadcrumb = ['Cadastros' => '', 'Benefício' => '', 'Alterar Benefício' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('id', 'action', 'breadcrumb', 'suppliers', 'benefit_types', 'states'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(16, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Benefit->id = $id;
        $this->request->data = $this->Benefit->read();

        $this->request->data['Benefit']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Benefit']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Benefit->save($this->request->data)) {
            $this->Flash->set(__('O Benefício foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
