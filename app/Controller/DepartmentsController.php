<?php
class DepartmentsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Department', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Department.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(20, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Department.name LIKE' => "%".$_GET['q']."%", 'Department.email LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Department', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Departamentos';
        $breadcrumb = ['Configuração' => '', 'Departamentos' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(20, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Department->create();
            if ($this->Department->validates()) {
                $this->request->data['Department']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Department->save($this->request->data)) {
                    $this->Session->setFlash(__('O departamento foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Session->setFlash(__('O departamento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Session->setFlash(__('O departamento não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Departamentos';
        $breadcrumb = ['Configuração' => '', 'Departamentos' => '', 'Novo departamento' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(20, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Department->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Department->validates();
            $this->request->data['Department']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Department->save($this->request->data)) {
                $this->Session->setFlash(__('O departamento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('O departamento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Department->validationErrors;
        $this->request->data = $this->Department->read();
        $this->Department->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Departamentos';
        $breadcrumb = ['Configuração' => '', 'Departamentos' => '', 'Alterar departamento' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(20, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Department->id = $id;
        $this->request->data = $this->Department->read();

        $this->request->data['Department']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Department']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Department->save($this->request->data)) {
            $this->Session->setFlash(__('O departamento foi excluidi com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
