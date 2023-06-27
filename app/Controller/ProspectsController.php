<?php
class ProspectsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Prospect', 'Status', 'Resale'];

    public $paginate = [
        'limit' => 100, 'order' => ['Status_id' => 'desc', 'Prospect.created' => 'desc', 'Prospect.empresa' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(13, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Prospect.resale_id' => CakeSession::read("Auth.User.resales")], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Prospect.empresa LIKE' => "%".$_GET['q']."%", 'Prospect.contato LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        
        $data = $this->Paginator->paginate('Prospect', $condition);
        $statuses = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Prospects';
        $this->set(compact('data', 'statuses', 'action'));
    }
    
    public function add()
    {
        $this->Permission->check(13, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is('post')) {
            $this->Prospect->create();
            if ($this->Prospect->validates()) {
                $this->request->data['Prospect']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Prospect->save($this->request->data)) {
                    $this->Session->setFlash(__('O cadastro foi realizado com sucesso'), 'default', ['class' => "alert alert-success"]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Session->setFlash(__('O cadastro não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
                }
            } else {
                $this->Session->setFlash(__('O cadastro não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $resales = $this->Resale->find("list", ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], 'order' => ['Resale.nome_fantasia' => 'asc']]);

        $breadcrumb = ['Nova Empresa' => ''];
        $this->set("action", "Prospects");
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'resales', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(13, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Prospect->id = $id;
        if ($this->request->is('post')) {
            $this->Prospect->validates();
            $this->request->data['Prospect']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Prospect->save($this->request->data)) {
                $this->Session->setFlash(__('Os dados foram alterados com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('Os dados não poderam ser alterados, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->Prospect->validationErrors;
        $this->request->data = $this->Prospect->read();
        $this->Prospect->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $breadcrumb = [$this->request->data['Prospect']['empresa'] => '', 'Alterar Empresa' => ''];
        $this->set("action", 'Prospects');
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(13, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Prospect->id = $id;
        $this->request->data = $this->Prospect->read();

        $this->request->data['Prospect']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Prospect']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Prospect->save($this->request->data)) {
            $this->Session->setFlash(__('Os dados foram excluídos com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        }
    }
}
