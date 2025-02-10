<?php
class TecnologiasController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Tecnologia', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Tecnologia.name' => 'asc']
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
            $condition['or'] = array_merge($condition['or'], ['Tecnologia.name LIKE' => "%".$_GET['q']."%", 'Tecnologia.description LIKE' => "%".$_GET['q']."%"], ['customer_id' => null]);
        }
    
        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']], ['customer_id' => 0]);
        }

        $condition['and'] = array_merge($condition['and'],  ['customer_id' => 0]);
        
        $data = $this->Paginator->paginate('Tecnologia', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
    
        $action = 'Tecnologia';
        $breadcrumb = ['Cadastros' => '', 'Tecnologia' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    
    public function add()
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Tecnologia->create();
            if ($this->Tecnologia->validates()) {
                $this->request->data['Tecnologia']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Tecnologia->save($this->request->data)) {
                    $this->Flash->set(__('A tecnologia foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->set(__('A tecnologia não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A tecnologia não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Tecnologia';
        $breadcrumb = ['Cadastros' => '', 'Tecnologia' => '', 'Nova tecnologia' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Tecnologia->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Tecnologia->validates();
            $this->request->data['Tecnologia']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Tecnologia->save($this->request->data)) {
                $this->Flash->set(__('A Tecnologia foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('A Tecnologia não pode ser alterada , Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Tecnologia->validationErrors;
        $this->request->data = $this->Tecnologia->read();
        $this->Tecnologia->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Tecnologia';
        $breadcrumb = ['Cadastros' => '', 'Tecnologia' => '', 'Alterar tecnologia' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(16, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Tecnologia->id = $id;
        $this->request->data = $this->Tecnologia->read();

        $this->request->data['Tecnologia']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Tecnologia']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Tecnologia->save($this->request->data)) {
            $this->Flash->set(__('A tecnologia foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
