<?php
class ModalidadesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Modalidade', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Modalidade.name' => 'asc']
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
            $condition['or'] = [
                'Modalidade.name LIKE' => "%" . $_GET['q'] . "%",
                'Modalidade.id LIKE' => "%" . $_GET['q'] . "%"
            ];
        }
    
        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'][] = ['Status.id' => $_GET['t']];
        }
        
        $data = $this->Paginator->paginate('Modalidade', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);
    
        $action = 'Modalidade';
        $breadcrumb = ['Cadastros' => '', 'Modalidade' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    
    public function add()
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Modalidade->create();
            if ($this->Modalidade->validates()) {
                $this->request->data['Modalidade']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Modalidade->save($this->request->data)) {
                    $this->Flash->set(__('A modalidade foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->set(__('A modalidade não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A modalidade não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Modalidade';
        $breadcrumb = ['Cadastros' => '', 'Modalidade' => '', 'Nova modalidade' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Modalidade->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Modalidade->validates();
            $this->request->data['Modalidade']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Modalidade->save($this->request->data)) {
                $this->Flash->set(__('A Modalidade foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('A Modalidade não pode ser alterada , Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Modalidade->validationErrors;
        $this->request->data = $this->Modalidade->read();
        $this->Modalidade->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Modalidade';
        $breadcrumb = ['Cadastros' => '', 'Modalidade' => '', 'Alterar modalidade' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(16, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Modalidade->id = $id;
        $this->request->data = $this->Modalidade->read();

        $this->request->data['Modalidade']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Modalidade']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Modalidade->save($this->request->data)) {
            $this->Flash->set(__('A modalidade foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
