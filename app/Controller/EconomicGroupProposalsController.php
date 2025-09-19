<?php

class EconomicGroupProposalsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['EconomicGroupProposal', 'Status', 'Customer'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'EconomicGroupProposal.name' => 'asc'],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id, $economicGroupId)
    {
        $this->Permission->check(11, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['EconomicGroupProposal.customer_id' => $id, 'EconomicGroupProposal.economic_group_id' => $economicGroupId], 'or' => []];

        if (isset($_GET['q']) and $_GET['q'] != '') {
            $condition['or'] = array_merge($condition['or'], ['EconomicGroupProposal.number LIKE' => '%'.$_GET['q'].'%']);
        }

        if (isset($_GET['t']) and $_GET['t'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('EconomicGroupProposal', $condition);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Propostas Grupo Econômico';

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 
            'Propostas Grupo Econômico' => ''
        ];
        
        $this->set(compact('data', 'action', 'breadcrumb', 'id', 'economicGroupId'));
    }

    public function add($id, $economicGroupId)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');

        if ($this->request->is(['post', 'put'])) {
            $this->request->data['EconomicGroupProposal']['customer_id'] = $id;
            $this->request->data['EconomicGroupProposal']['economic_group_id'] = $economicGroupId;
            $this->request->data['EconomicGroupProposal']['number'] = $this->EconomicGroupProposal->getNextNumber();
            $this->request->data['EconomicGroupProposal']['user_creator_id'] = CakeSession::read('Auth.User.id');

            $this->EconomicGroupProposal->create();
            if ($this->EconomicGroupProposal->save($this->request->data)) {
                $newId = $this->EconomicGroupProposal->id;
                $newStatus = (int)$this->request->data['EconomicGroupProposal']['status_id'];

                if ($newStatus == 99) {
                    $this->EconomicGroupProposal->unbindModel(['belongsTo' => ['Customer', 'Status']]);
                    $this->EconomicGroupProposal->updateAll(
                        ['EconomicGroupProposal.status_id' => 92, 'EconomicGroupProposal.cancelled_description' => "'Cancelado por ativação de outra proposta'"],
                        ['EconomicGroupProposal.customer_id' => $id, 'EconomicGroupProposal.economic_group_id' => $economicGroupId, 'EconomicGroupProposal.status_id !=' => 92, 'EconomicGroupProposal.id !=' => $newId]
                    );
                }
                $this->Flash->set(__('A proposta foi salva com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id, $economicGroupId]);
            } else {
                $this->Flash->set(__('A proposta não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 20]]);

        $disabled = false;

        $action = 'Propostas Grupo Econômico';
        $breadcrumb = [$cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 'Propostas Grupo Econômico' => '', 'Nova proposta' => ''];
        
        $this->set('form_action', 'add/'.$id.'/'.$economicGroupId);
        $this->set(compact('action', 'breadcrumb', 'id', 'economicGroupId', 'statuses', 'disabled'));
    }

    public function edit($id, $economicGroupId, $EconomicGroupProposalId = null)
    {
        $this->Permission->check(11, 'escrita') ? '' : $this->redirect('/not_allowed');

        $this->EconomicGroupProposal->id = $EconomicGroupProposalId;

        if ($this->request->is(['post', 'put'])) {
            $old = $this->EconomicGroupProposal->find('first', ['conditions' => ['EconomicGroupProposal.id' => $EconomicGroupProposalId], 'fields' => ['EconomicGroupProposal.status_id']]);
            $old_status = (int)$old['EconomicGroupProposal']['status_id'];

            $this->request->data['EconomicGroupProposal']['user_updated_id'] = CakeSession::read('Auth.User.id');
            if ($this->EconomicGroupProposal->save($this->request->data)) {

                $newStatus = (int)$this->request->data['EconomicGroupProposal']['status_id'];

                if ($newStatus == 99 && $newStatus != $old_status) {
                    $this->EconomicGroupProposal->unbindModel(['belongsTo' => ['Customer', 'Status']]);
                    $this->EconomicGroupProposal->updateAll(
                        ['EconomicGroupProposal.status_id' => 92, 'EconomicGroupProposal.cancelled_description' => "'Cancelado por ativação de outra proposta'"],
                        ['EconomicGroupProposal.customer_id' => $id, 'EconomicGroupProposal.economic_group_id' => $economicGroupId, 'EconomicGroupProposal.status_id !=' => 92, 'EconomicGroupProposal.id !=' => $EconomicGroupProposalId]
                    );
                }

                $this->Flash->set(__('A proposta foi alterada com sucesso'), ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id, $economicGroupId]);
            } else {
                $this->Flash->set(__('A proposta não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => 'alert alert-danger']]);
            }
        }       

        $temp_errors = $this->EconomicGroupProposal->validationErrors;
        $this->request->data = $this->EconomicGroupProposal->read();
        $this->EconomicGroupProposal->validationErrors = $temp_errors;

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 20]]);

        $disabled = in_array($this->request->data['EconomicGroupProposal']['status_id'], [92, 93]);

        $action = 'Propostas';
        $breadcrumb = [$cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id], 'Propostas' => '', 'Alterar proposta' => ''];
        
        $this->set('form_action', 'edit/'.$id.'/'.$economicGroupId);
        $this->set(compact('id', 'economicGroupId', 'EconomicGroupProposalId', 'action', 'breadcrumb', 'statuses', 'disabled'));

        $this->render('add');
    }

    public function delete($id)
    {
        $this->Permission->check(11, 'excluir') ? '' : $this->redirect('/not_allowed');

        $this->EconomicGroupProposal->id = $id;
        $this->request->data = $this->EconomicGroupProposal->read();

        $this->request->data['EconomicGroupProposal']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['EconomicGroupProposal']['usuario_id_cancel'] = CakeSession::read('Auth.User.id');

        if ($this->EconomicGroupProposal->save($this->request->data)) {
            $this->Flash->set(__('A proposta foi excluida com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect($this->referer());
        }
    }
}
