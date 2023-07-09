<?php
class NoticesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Notice', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Notice.title' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(55, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Notice.title LIKE' => "%".$_GET['q']."%", 'Notice.description LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Notice', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Avisos';
        $breadcrumb = ['Lista' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(55, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['Notice']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->Notice->create();
            if ($this->Notice->save($this->request->data)) {
                $this->Session->setFlash(__('O aviso foi salvo com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('O aviso não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $checkActivated = $this->Notice->find('count', ['conditions' => ['Notice.status_id' => 1]]);
        $not = [];
        if ($checkActivated) {
            $not = ['not' => ['Status.id' => 1]];
        }
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1, $not]]);

        $action = 'Avisos';
        $breadcrumb = ['Novo aviso' => ''];
        $this->set("form_action", "add", "checkActivated");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(55, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Notice->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Notice->validates();
            $this->request->data['Notice']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Notice->save($this->request->data)) {
                $this->Session->setFlash(__('O aviso foi alterado com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('O aviso não pode ser alterado, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->Notice->validationErrors;
        $this->request->data = $this->Notice->read();
        $this->Notice->validationErrors = $temp_errors;
            
        $checkActivated = $this->Notice->find('count', ['conditions' => ['Notice.status_id' => 1, 'not' => ['Notice.id' => $id]]]);
        $not = [];
        if ($checkActivated) {
            $not = ['not' => ['Status.id' => 1]];
        }
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1, $not]]);

        $action = 'Avisos';
        $breadcrumb = ['Alterar aviso' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(55, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Notice->id = $id;
        $this->request->data = $this->Notice->read();

        $this->request->data['Notice']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Notice']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Notice->save($this->request->data)) {
            $this->Session->setFlash(__('O aviso foi excluido com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        }
    }
}
