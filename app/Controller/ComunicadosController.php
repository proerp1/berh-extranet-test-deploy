<?php
class ComunicadosController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Comunicado', 'Permission', 'Status', 'Categoria'];

    public $paginate = [
        'limit' => 47,
        'order' => [
            'data' => 'asc',
            'Status.id' => 'asc',
            'Comunicado.titulo' => 'asc'
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(2, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Comunicado.titulo LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Comunicado', $condition);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Comunicados';
        $breadcrumb = ['Configurações' => '', 'Comunicados' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Comunicado->create();
            if ($this->Comunicado->validates()) {
                $this->request->data['Comunicado']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Comunicado->save($this->request->data)) {
                    $this->Flash->set(__('O Comunicado foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => "../comunicados/index"]);
                    
                } else {
                    $this->Flash->set(__('O Comunicado não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O Comunicado não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $categorias = $this->Categoria->find('list');
        $action = 'Comunicados';
        $breadcrumb = ['Configurações' => '', 'Comunicados' => '', 'Novo Comunicado' => ''];
        $this->set(compact('categorias', 'action', 'breadcrumb'));
        $this->set("action", "Novo Comunicado");
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Comunicado->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Comunicado->validates();
            if ($this->request->data['Comunicado']['file'] == '') {
                unset($this->request->data['Comunicado']['file']);
            }
            if ($this->Comunicado->save($this->request->data)) {
                $this->Flash->set(__('O Comunicado foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'edit/'.$this->Comunicado->id]);
            } else {
                $this->Flash->set(__('O Comunicado não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Comunicado->validationErrors;
        $this->request->data = $this->Comunicado->read();
        $this->Comunicado->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $categorias = $this->Categoria->find('list');
        $action = 'Comunicados';
        $breadcrumb = ['Configurações' => '', 'Comunicados' => '', 'Alterar Comunicado' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('categorias', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(2, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Comunicado->id = $id;
        $this->request->data = $this->Comunicado->read();

        $this->request->data['Comunicado']['data_cancel'] = date("Y-m-d H:i:s");
         $this->request->data['Comunicado']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");
        

        if ($this->Comunicado->save($this->request->data)) {
            $this->Flash->set(__('O Comunicado foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    
}
