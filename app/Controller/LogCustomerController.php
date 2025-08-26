<?php

class LogCustomerController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'HtmltoPdf', 'ExcelGenerator', 'Robo'];
    public $uses = ['Customer', 'LogCustomer'];

    public $paginate = [];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id = null)
    {
        $this->Permission->check(3, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = array_merge($this->paginate, [
          'order' => ['LogCustomer.created' => 'desc']
        ]);
        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $condition = ['and' => ['LogCustomer.customer_id' => $id], 'or' => []];

        $data = $this->Paginator->paginate('LogCustomer', $condition);

        $breadcrumb = [
          $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
          'Histórico Alterações' => '',
        ];

        $this->set('action', 'Histórico Alterações');
        $this->set(compact('id', 'data', 'breadcrumb'));
    }
}
