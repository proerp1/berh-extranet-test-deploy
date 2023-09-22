<?php

class ProposalsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Proposal', 'Status', 'Customer'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'Proposal.name' => 'asc'],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Permission->check(11, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['Proposal.customer_id' => $id], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != '') {
            $condition['or'] = array_merge($condition['or'], ['Proposal.number LIKE' => '%'.$_GET['q'].'%']);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Proposal', $condition);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Propostas';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 
            'Propostas' => ''
        ];
        $this->set(compact('data', 'action', 'breadcrumb', 'id'));
    }

    public function add($id)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');

        if ($this->request->is(['post', 'put'])) {
            $this->request->data['Proposal']['number'] = $this->Proposal->getNextNumber();
            $this->request->data['Proposal']['customer_id'] = $id;
            $this->request->data['Proposal']['user_creator_id'] = CakeSession::read('Auth.User.id');

            $this->Proposal->create();
            if ($this->Proposal->save($this->request->data)) {
                $this->Flash->set(__('A proposta foi salva com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set(__('A proposta não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 20]]);

        $action = 'Propostas';
        $breadcrumb = [$cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 'Propostas' => '', 'Nova proposta' => ''];
        $this->set('form_action', 'add/'.$id);
        $this->set(compact('action', 'breadcrumb', 'id', 'statuses'));
    }

    public function edit($id, $proposalId = null)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');
        $this->Proposal->id = $proposalId;
        if ($this->request->is(['post', 'put'])) {

            $this->request->data['Proposal']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->Proposal->save($this->request->data)) {
                $this->Flash->set(__('A proposta foi alterada com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set(__('A proposta não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->Proposal->validationErrors;
        $this->request->data = $this->Proposal->read();
        $this->Proposal->validationErrors = $temp_errors;

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 20]]);

        $action = 'Propostas';
        $breadcrumb = [$cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 'Propostas' => '', 'Alterar proposta' => ''];
        $this->set('form_action', 'edit/'.$id);
        $this->set(compact('id', 'proposalId', 'action', 'breadcrumb', 'statuses'));

        $this->render('add');
    }

    public function delete($id)
    {
        $this->Permission->check(11, 'excluir') ? '' : $this->redirect('/not_allowed');
        $this->Proposal->id = $id;
        $this->request->data = $this->Proposal->read();

        $this->request->data['Proposal']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['Proposal']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->Proposal->save($this->request->data)) {
            $this->Flash->set(__('A proposta foi excluida com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect($this->referer());
        }
    }
}
