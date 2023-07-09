<?php
class RevenuesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Revenue', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Revenue.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(13, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Revenue.name LIKE' => "%" . $_GET['q'] . "%", 'Revenue.description LIKE' => "%" . $_GET['q'] . "%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Revenue', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Receitas';
        $breadcrumb = ['Cadastros' => '', 'Receitas' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(13, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Revenue->create();
            if ($this->Revenue->validates()) {
                $this->request->data['Revenue']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Revenue->save($this->request->data)) {
                    $this->Session->setFlash(__('A receita foi salva com sucesso'), 'default', ['class' => "alert alert-success"]);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Session->setFlash(__('A receita não pode ser salva, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
                }
            } else {
                $this->Session->setFlash(__('A receita não pode ser salva, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Receitas';
        $breadcrumb = ['Cadastros' => '', 'Receitas' => '', 'Nova receita' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(13, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Revenue->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Revenue->validates();
            $this->request->data['Revenue']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->Revenue->save($this->request->data)) {
                $this->Session->setFlash(__('A receita foi alterada com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__('A receita não pode ser alterada, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->Revenue->validationErrors;
        $this->request->data = $this->Revenue->read();
        $this->Revenue->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Receitas';
        $breadcrumb = ['Cadastros' => '', 'Receitas' => '', 'Alterar receita' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(13, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Revenue->id = $id;
        $this->request->data = $this->Revenue->read();

        $this->request->data['Revenue']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Revenue']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Revenue->save($this->request->data)) {
            $this->Session->setFlash(__('A receita foi excluida com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        }
    }
}
