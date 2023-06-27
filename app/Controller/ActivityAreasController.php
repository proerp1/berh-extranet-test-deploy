<?php
class ActivityAreasController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['ActivityArea', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'ActivityArea.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(17, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['ActivityArea.name LIKE' => "%".$_GET['q']."%", 'ActivityArea.description LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('ActivityArea', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Ramo de atividades';
        $breadcrumb = ['Cadastros' => '', 'Ramo de atividades' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(17, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is('post')) {
            $this->ActivityArea->create();
            if ($this->ActivityArea->validates()) {
                $this->request->data['ActivityArea']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->ActivityArea->save($this->request->data)) {
                    $this->Session->setFlash(__('O ramo de atividade foi salvo com sucesso'), 'default', ['class' => "alert alert-success"]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Session->setFlash(__('O ramo de atividade não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
                }
            } else {
                $this->Session->setFlash(__('O ramo de atividade não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Ramo de atividades';
        $breadcrumb = ['Cadastros' => '', 'Ramo de atividades' => '', 'Novo ramo de atividade' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(17, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->ActivityArea->id = $id;
        if ($this->request->is('post')) {
            $this->ActivityArea->validates();
            $this->request->data['ActivityArea']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->ActivityArea->save($this->request->data)) {
                $this->Session->setFlash(__('O ramo de atividade foi alterado com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('O ramo de atividade não pode ser alterado, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->ActivityArea->validationErrors;
        $this->request->data = $this->ActivityArea->read();
        $this->ActivityArea->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Ramo de atividades';
        $breadcrumb = ['Cadastros' => '', 'Ramo de atividades' => '', 'Alterar ramo de atividade' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(17, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->ActivityArea->id = $id;
        $this->request->data = $this->ActivityArea->read();

        $this->request->data['ActivityArea']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['ActivityArea']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->ActivityArea->save($this->request->data)) {
            $this->Session->setFlash(__('O ramo de atividade foi excluido com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        }
    }
}
