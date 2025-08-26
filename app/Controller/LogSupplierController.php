<?php

class LogSupplierController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'HtmltoPdf', 'ExcelGenerator', 'Robo'];
    public $uses = ['Supplier', 'LogSupplier'];

    public $paginate = [];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id = null)
    {
        $this->Permission->check(9, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = array_merge($this->paginate, [
          'order' => ['LogSupplier.created' => 'desc']
        ]);
        $this->Supplier->id = $id;
        $cliente = $this->Supplier->read();

        $condition = ['and' => ['LogSupplier.supplier_id' => $id], 'or' => []];

        $data = $this->Paginator->paginate('LogSupplier', $condition);

        $breadcrumb = [
          $cliente['Supplier']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
          'Histórico Alterações' => '',
        ];

        $this->set('action', 'Histórico Alterações');
        $this->set(compact('id', 'data', 'breadcrumb'));
    }
}
