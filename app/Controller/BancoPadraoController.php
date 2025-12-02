<?php
class BancoPadraoController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'ExcelGenerator'];
    public $uses = ['Bank', 'BancoPadrao'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(97, "escrita") ? "" : $this->redirect("/not_allowed");

        $banks = $this->Bank->find('list', ['conditions' => ['Bank.id' => [1,9]]]);

        $current = $this->BancoPadrao->find('first');

        if ($this->request->is(['post'])) {
            $this->request->data['BancoPadrao']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if (!empty($current)) {
                $this->BancoPadrao->id = $current['BancoPadrao']['id'];
            }
            if ($this->BancoPadrao->save($this->request->data)) {
                $this->Flash->set(__('O banco padrão foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            }

            $this->redirect($this->referer());
        }

        $action = "Banco padrão";
        $this->set(compact('banks', 'action', 'current'));
    }
}
