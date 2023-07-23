<?php
class PefinMaintenancesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];

    public $paginate = ['limit' => 10, 'order' => ['Status.id' => 'asc', 'PefinMaintenance.description' => 'asc']];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }
    
    public function index()
    {
        $this->Permission->check(8, "escrita") ? "" : $this->redirect("/not_allowed");

        $pefin = $this->PefinMaintenance->find('first');
        $this->PefinMaintenance->id = $pefin['PefinMaintenance']['id'];
        if ($this->request->is(['post', 'put'])) {
            $update_data = ['PefinMaintenance.data_cancel' => 'current_timestamp()', 'PefinMaintenance.user_updated_id' => CakeSession::Read('Auth.User.id')];
            $this->PefinMaintenance->updateAll(
                $update_data//set
            );

            $this->PefinMaintenance->create();
            $this->PefinMaintenance->validates();
            $this->request->data['PefinMaintenance']['user_created_id'] = CakeSession::Read('Auth.User.id');
            if ($this->PefinMaintenance->save($this->request->data)) {
                $this->Flash->set(__('Salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('Não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->PefinMaintenance->validationErrors;
        $this->request->data = $this->PefinMaintenance->read();
        $this->PefinMaintenance->validationErrors = $temp_errors;
        
        $action = 'Manutenção Pefin';
        $breadcrumb = ['Configurações' => '', 'Manutenção Pefin' => ''];
        $this->set("form_action", "index");
        $this->set(compact('action', 'breadcrumb'));
    }
}
