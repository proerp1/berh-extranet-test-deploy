<?php

class SupplierParamsGeController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['SupplierParamsGe', 'Status', 'Customer', 'Supplier'];

    public $paginate = [
        'limit' => 10, 'order' => ['SupplierParamsGe.id' => 'asc'],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Permission->check(82, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['SupplierParamsGe.supplier_id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != '') {
            $condition['or'] = array_merge($condition['or'], ['Supplier.nome_fantasia LIKE' => '%'.$_GET['q'].'%']);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('SupplierParamsGe', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $this->Supplier->id = $id;
        $this->Supplier->recursive = -1;
        $supplier = $this->Supplier->read();

        $action = 'Parâmetros GE';

        $breadcrumb = [
            $supplier['Supplier']['nome_fantasia'] => ['controller' => 'customers', 'action' => 'edit', $id],
            $action => '',
        ];
        $this->set(compact('status', 'data', 'action', 'breadcrumb', 'id'));
    }

    public function add($id)
    {
        $this->Permission->check(82, 'escrita') ? '' : $this->redirect('/not_allowed');
        if ($this->request->is('post')) {
            $this->request->data['SupplierParamsGe']['supplier_id'] = $id;
            $this->request->data['SupplierParamsGe']['user_creator_id'] = CakeSession::read('Auth.CustomerUser.id');

            $this->SupplierParamsGe->create();
            if ($this->SupplierParamsGe->save($this->request->data)) {
                $this->Flash->set('O Parâmetro GE adicionado com sucesso.', ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set('Falha ao adicionar parâmetro GE. Por favor, tente novamente.', ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $customers = $this->Customer->find('list', ['conditions' => ['Customer.status_id' => 3], 'order' => 'Customer.nome_secundario']);

        $this->Supplier->id = $id;
        $this->Supplier->recursive = -1;
        $supplier = $this->Supplier->read();

        $action = 'Novo parâmetro GE';
        $breadcrumb = [
            $supplier['Supplier']['nome_fantasia'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Parâmetros GE' => ['controller' => 'group_economics', 'action' => 'index'],
            'Novo' => '',
        ];
        $this->set(compact('action', 'breadcrumb', 'statuses', 'customers', 'id'));
    }

    public function edit($id, $paramsGeId = null)
    {
        $this->Permission->check(82, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->SupplierParamsGe->id = $paramsGeId;
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['SupplierParamsGe']['user_updated_id'] = CakeSession::read('Auth.User.id');

            if ($this->SupplierParamsGe->save($this->request->data)) {
                $this->Flash->set(__('O Parâmetro GE foi alterado com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set(__('O Parâmetro GE não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->SupplierParamsGe->validationErrors;
        $this->request->data = $this->SupplierParamsGe->read();
        $this->SupplierParamsGe->validationErrors = $temp_errors;

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $customers = $this->Customer->find('list', ['conditions' => ['Customer.status_id' => 3], 'order' => 'Customer.nome_secundario']);

        $this->Supplier->id = $id;
        $this->Supplier->recursive = -1;
        $supplier = $this->Supplier->read();

        $action = 'Alterar parâmetro GE';
        $breadcrumb = [
            $supplier['Supplier']['nome_fantasia'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Parâmetros GE' => ['controller' => 'group_economics', 'action' => 'index'],
            'Alterar' => '',
        ];

        $this->set(compact('action', 'breadcrumb', 'statuses', 'customers', 'id', 'paramsGeId'));
        $this->render('add');
    }

    public function delete($id)
    {
        $this->Permission->check(82, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->SupplierParamsGe->id = $id;
        $this->request->data = $this->SupplierParamsGe->read();

        $this->request->data['SupplierParamsGe']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['SupplierParamsGe']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->SupplierParamsGe->save($this->request->data)) {
            $this->Flash->set(__('O Parâmetro GE foi excluído com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect($this->referer());
        }
    }
}
