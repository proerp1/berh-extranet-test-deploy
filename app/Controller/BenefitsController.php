<?php
class BenefitsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Benefit', 'Status'];

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
            $condition['or'] = array_merge($condition['or'], ['Benefit.name LIKE' => "%".$_GET['q']."%", 'Benefit.description LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Benefit', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Centro de Custo';
        $breadcrumb = ['Cadastros' => '', 'Centro de Custo' => ''];
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
                    $this->Session->setFlash(__('O centro de custo foi salvo com sucesso'), 'default', ['class' => "alert alert-success"]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Session->setFlash(__('O centro de custo não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
                }
            } else {
                $this->Session->setFlash(__('O centro de custo não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
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
        $this->Benefit->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Benefit->validates();
            $this->request->data['Benefit']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Benefit->save($this->request->data)) {
                $this->Session->setFlash(__('O centro de custo foi alterado com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('O centro de custo não pode ser alterado, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->Benefit->validationErrors;
        $this->request->data = $this->Benefit->read();
        $this->Benefit->validationErrors = $temp_errors;
        
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
        $this->Benefit->id = $id;
        $this->request->data = $this->Benefit->read();

        $this->request->data['Benefit']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Benefit']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Benefit->save($this->request->data)) {
            $this->Session->setFlash(__('O centro de custo foi excluido com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        }
    }
}
