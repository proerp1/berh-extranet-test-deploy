<?php
class UserResalesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['UserResale', 'User', 'Status', 'Resale'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'UserResale.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Permission->check(60, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['UserResale.user_id' => $id], "or" => []];

        $data = $this->Paginator->paginate('UserResale', $condition);

        $cadastrados = $this->UserResale->find('all', ['conditions' => $condition]);
        $ids_cadastrados = [];
        foreach ($cadastrados as $resale) {
            $ids_cadastrados[] = $resale['UserResale']['resale_id'];
        }

        $resales = $this->Resale->find("list", ['conditions' => ['Resale.status_id' => 1, 'not' => ['Resale.id' => $ids_cadastrados]], 'order' => ['Resale.nome_fantasia' => 'asc']]);

        $resaleIds = [];
        foreach ($resales as $resale_id => $name) {
            $resaleIds[$resale_id] = $name;
        }

        $this->User->id = $id;
        $cliente = $this->User->read();

        $action = $cliente['User']['name'].' - Franquias';

        $action = 'Franquias';
        $breadcrumb = ['Configurações' => '', $cliente['User']['name'] => '', 'Franquias' => ''];
        $this->set(compact('data', 'id', 'action', 'breadcrumb', 'resaleIds'));
    }
    
    public function add($id)
    {
        $this->Permission->check(60, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['UserResale']['user_id'] = $id;
            $this->request->data['UserResale']['user_creator_id'] = CakeSession::read("Auth.User.id");

            $this->UserResale->create();
            if ($this->UserResale->save($this->request->data)) {
                $this->Session->setFlash(__('A franquia foi salva com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(__('A franquia não pode ser salva, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }
    }

    public function delete($id)
    {
        $this->Permission->check(60, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->UserResale->id = $id;

        $data['UserResale']['data_cancel'] = date("Y-m-d H:i:s");
        $data['UserResale']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->UserResale->save($data)) {
            $this->Session->setFlash(__('A franquia foi excluida com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect($this->referer());
        }
    }
}
