<?php
class CostCentersController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['CostCenter', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'CostCenter.name' => 'asc']
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
            $condition['or'] = array_merge($condition['or'], ['CostCenter.name LIKE' => "%".$_GET['q']."%", 'CostCenter.description LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('CostCenter', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Centro de Custo';
        $breadcrumb = ['Cadastros' => '', 'Centro de Custo' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->CostCenter->create();
            if ($this->CostCenter->validates()) {
                $this->request->data['CostCenter']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->CostCenter->save($this->request->data)) {
                    $this->Session->setFlash(__('O centro de custo foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Session->setFlash(__('O centro de custo não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Session->setFlash(__('O centro de custo não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Centro de Custo';
        $breadcrumb = ['Cadastros' => '', 'Centro de Custo' => '', 'Novo centro de custo' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->CostCenter->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->CostCenter->validates();
            $this->request->data['CostCenter']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->CostCenter->save($this->request->data)) {
                $this->Session->setFlash(__('O centro de custo foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('O centro de custo não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->CostCenter->validationErrors;
        $this->request->data = $this->CostCenter->read();
        $this->CostCenter->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Centro de Custo';
        $breadcrumb = ['Cadastros' => '', 'Centro de Custo' => '', 'Alterar centro de custo' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(16, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->CostCenter->id = $id;
        $this->request->data = $this->CostCenter->read();

        $this->request->data['CostCenter']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['CostCenter']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->CostCenter->save($this->request->data)) {
            $this->Session->setFlash(__('O centro de custo foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
