<?php

App::uses('AppController', 'Controller');

class EconomicGroupsController extends AppController
{
    public $components = ['Paginator', 'Permission'];
    public $uses = ['EconomicGroup', 'Status', 'Customer'];

    public function index($id)
    {
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['EconomicGroup.customer_id' => $id], 'or' => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['EconomicGroup.name LIKE' => '%'.$_GET['q'].'%', 'EconomicGroup.razao_social LIKE' => '%'.$_GET['q'].'%']);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['EconomicGroup.status_id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('EconomicGroup', $condition);
        $status = $this->Status->find("all", ["conditions" => ["Status.categoria" => 1]]);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Grupos Econômicos';
        
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 
            'Grupos Econômicos' => ''
        ];
        $this->set(compact('data', 'action', 'id', 'breadcrumb', 'status'));
    }

    public function add($id)
    {
        if ($this->request->is('post')) {
            $this->request->data['EconomicGroup']['customer_id'] = $id;
            $this->request->data['EconomicGroup']['user_creator_id'] = CakeSession::read("Auth.CustomerUser.id");
            
            $this->EconomicGroup->create();
            if ($this->EconomicGroup->save($this->request->data)) {
                $this->Flash->set('grupo econômico adicionado com sucesso.', ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set('Falha ao adicionar grupo econômico. Por favor, tente novamente.', ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $statuses = $this->Status->find("list", ["conditions" => ["Status.categoria" => 1]]);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Novo grupo econômico';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 
            'Grupos Econômicos' => ['controller' => 'group_economics', 'action' => 'index'],
            'Novo grupo econômico' => '',
        ];
        $this->set(compact('action', 'breadcrumb', 'statuses', 'id'));
    }

    public function edit($id, $economicGroupId = null)
    {
        $economicGroup = $this->EconomicGroup->findById($economicGroupId);
        if (!$economicGroup) {
            $this->Flash->set('Grupo econômico não encontrado.', ['params' => ['class' => 'alert alert-danger']]);
            $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->EconomicGroup->id = $economicGroupId;
            $this->request->data['EconomicGroup']['user_updated_id'] = CakeSession::read("Auth.CustomerUser.id");
            
            if ($this->EconomicGroup->save($this->request->data)) {
                $this->Flash->set('Grupo econômico atualizado com sucesso.', ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set('Falha ao atualizar grupo econômico. Por favor, tente novamente.', ['params' => ['class' => 'alert alert-danger']]);
            }
        }
        
        $temp_errors = $this->EconomicGroup->validationErrors;
        $this->request->data = $economicGroup;
        $this->EconomicGroup->validationErrors = $temp_errors;

        $statuses = $this->Status->find("list", ["conditions" => ["Status.categoria" => 1]]);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Novo grupo econômico';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 
            'Grupos Econômicos' => ['controller' => 'group_economics', 'action' => 'index'],
            $this->request->data['EconomicGroup']['name'] => '',
        ];
        $this->set(compact('action', 'breadcrumb', 'id', 'economicGroupId', 'statuses'));
        $this->render('add');
    }

    public function delete($id = null)
    {
  

        $this->EconomicGroup->id = $id;
        $this->request->data = $this->EconomicGroup->read();

        $this->request->data['EconomicGroup']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['EconomicGroup']['usuario_id_cancel'] = CakeSession::read('Auth.CustomerUser.id');

        if ($this->EconomicGroup->save($this->request->data)) {
            $this->Flash->set(__('O usuário foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index', $this->request->data['EconomicGroup']['customer_id']]);
        }
    }
}
