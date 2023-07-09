<?php
class GroupsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Group', 'Permission', 'Status'];

    public $paginate = [
        'limit' => 10,
        'order' => [
            'Status.id' => 'asc',
            'Group.name' => 'asc'
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
            $condition['or'] = array_merge($condition['or'], ['Group.name LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Group', $condition);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Grupos';
        $breadcrumb = ['Configurações' => '', 'Grupos' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Group->create();
            if ($this->Group->validates()) {
                $this->request->data['Group']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->Group->save($this->request->data)) {
                    $this->Session->setFlash(__('O grupo foi salvo com sucesso'), 'default', ['class' => "alert alert-success"]);
                    $this->redirect(['action' => 'edit/'.$this->Group->id]);
                } else {
                    $this->Session->setFlash(__('O grupo não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
                }
            } else {
                $this->Session->setFlash(__('O grupo não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Grupos';
        $breadcrumb = ['Configurações' => '', 'Grupos' => '', 'Novo grupo' => ''];
        $this->set(compact('statuses', 'action', 'breadcrumb'));
        $this->set("action", "Novo Grupo");
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Group->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Group->validates();
            
            if ($this->Group->save($this->request->data)) {
                $this->Session->setFlash(__('O grupo foi alterado com sucesso'), 'default', ['class' => "alert alert-success"]);
            } else {
                $this->Session->setFlash(__('O grupo não pode ser alterado, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }

        $temp_errors = $this->Group->validationErrors;
        $this->request->data = $this->Group->read();
        $this->Group->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Grupos';
        $breadcrumb = ['Configurações' => '', 'Grupos' => '', 'Alterar grupo' => ''];
        $this->set("action", $this->request->data['Group']['name']);
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(2, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Group->id = $id;
        $this->request->data = $this->Group->read();

        $this->request->data['Group']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['Group']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->Group->save($this->request->data)) {
            $this->Session->setFlash(__('O grupo foi excluido com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function permission($id)
    {
        $this->Permission->check(2, "leitura") ? "" : $this->redirect("/not_allowed");

        $this->Group->id = $id;
        $this->request->data = $this->Group->read();

        $this->loadModel('Permission');
        $permissions = $this->Permission->get_permissions_by_group($id);

        $action = 'Grupos';
        $breadcrumb = ['Configurações' => '', 'Grupos' => '', $this->request->data['Group']['name'] => '', 'Alterar permissões' => ''];
        $this->set(compact('permissions', 'id', 'action', 'breadcrumb'));
    }

    public function alter_permission()
    {
        $this->Permission->check(2, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->loadModel('Permission');
        $this->Permission->deleteAll(['Permission.group_id' => $_POST["group_id"]], false);
        foreach ($_POST["permissoes"] as $page_id => $permission) {
            $this->Permission->create();
            $perm = ["Permission" =>
                                ["leitura" => $permission["leitura"],
                                    "escrita" => $permission["escrita"],
                                    "excluir" => $permission["excluir"],
                                    "page_id" => $page_id,
                                    "group_id" => $_POST["group_id"]
                                ]
            ];
            $this->Permission->save($perm);
        }
        $this->Session->setFlash(__('A permissao foi alterada com sucesso'), 'default', ['class' => "alert alert-success"]);
        $this->redirect("/groups/permission/".$_POST["group_id"]);
    }
}
