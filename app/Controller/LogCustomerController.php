<?php

class LogCustomerController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'HtmltoPdf', 'ExcelGenerator', 'Robo'];
    public $uses = ['Customer', 'LogCustomer', 'Log'];

    public $paginate = [];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id = null)
    {
        $this->Permission->check(94, 'leitura') ? '' : $this->redirect('/not_allowed');
        $this->Paginator->settings = array_merge($this->paginate, [
            'order' => ['Log.log_date' => 'desc'],
            'joins' => [
                [
                    'table' => 'users',
                    'alias' => 'Creator',
                    'type' => 'INNER',
                    'conditions' => ['Creator.id = Log.user_id']
                ],
            ],
            'fields' => ['Log.*', 'Creator.*']
        ]);
        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $tabelas_abas = [
            'Proposal',
            'CustomerUser',
            'Document',
            'EconomicGroup',
            'EconomicGroupProposal',
            'CustomerFile',
            'CustomerSupplierLogin',
            'CustomerAddress',
            'CustomerBenefitCode',
        ];

        $condition = [
            'and' => [],
            'or' => [
                'and' => ['Log.primary_key' => $id, 'Log.log_table' => 'Customer'],
                'and ' => ['Log.parent_log' => $id, 'Log.log_table' => $tabelas_abas]
            ]
        ];

        $data = $this->Paginator->paginate('Log', $condition);

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Histórico Alterações' => '',
        ];

        $this->set('action', 'Histórico Alterações');
        $this->set(compact('id', 'data', 'breadcrumb'));
    }
}
