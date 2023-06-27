<?php
class DadosComerciaisController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator'];
    public $uses = ['Customer', 'Seller'];

    public $paginate = [
        'Customer' => [
            'fields'  => [
                'Status.name',
                'Seller.nome_fantasia',
                'Plan.description',
                'Plan.value',
                'sum(Income.valor_total) as valor_em_aberto',
                'Customer.codigo_associado',
                'Customer.nome_secundario',
                'Customer.nome_primario',
                'Customer.responsavel',
                'Customer.telefone1',
                'Customer.telefone2',
                'Customer.celular',
                'Customer.celular1',
                'Customer.celular2',
                'Customer.endereco',
                'Customer.numero',
                'Customer.bairro',
                'Customer.cidade',
                'Customer.estado',
            ],
            'joins' => [
                [
                    'table' => 'sellers',
                    'alias' => 'Seller',
                    'type' => 'INNER',
                    'conditions' => ['Seller.id = Customer.seller_id']
                ],
                [
                    'table' => 'plan_customers',
                    'alias' => 'PlanCustomer',
                    'type' => 'INNER',
                    'conditions' => ['PlanCustomer.customer_id = Customer.id', 'PlanCustomer.data_cancel' => '1901-01-01']
                ],
                [
                    'table' => 'plans',
                    'alias' => 'Plan',
                    'type' => 'INNER',
                    'conditions' => ['Plan.id = PlanCustomer.plan_id']
                ],
                [
                    'table' => 'statuses',
                    'alias' => 'Status',
                    'type' => 'INNER',
                    'conditions' => ['Status.id = Customer.status_id']
                ],
                [
                    'table' => 'incomes',
                    'alias' => 'Income',
                    'type' => 'LEFT',
                    'conditions' => ['Income.customer_id = Customer.id', 'Income.data_cancel' => '1901-01-01', 'Income.status_id' => 15]
                ]
            ],
            'order' => ['Customer.codigo_associado' => 'asc'],
            'group' => ['Customer.id'],
            'limit' => 10,
            'recursive' => -1,
        ],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (!empty($_GET['v'])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.seller_id' => $_GET['v']]);
        }

        if (isset($_GET['exportar'])) {
            $nome = 'dados_comerciais.xlsx';

            unset($this->paginate['Customer']['limit']);

            $data = $this->Customer->find('all', Hash::merge(['conditions' => $condition], $this->paginate['Customer']));

            $this->ExcelGenerator->gerarDadosComerciais($nome, $data);
            $this->redirect("/files/excel/".$nome);
        } else {
            $data = $this->Paginator->paginate('Customer', $condition);
        }

        $sellers = $this->Seller->find('list', ['conditions' => ['Seller.status_id' => 1], 'order' => 'Seller.nome_fantasia']);
        
        $action = 'Dados comerciais';
        $breadcrumb = ['Relatórios' => '', 'Dados comerciais' => ''];
        $this->set(compact('data', 'sellers', 'action', 'breadcrumb'));
    }
}
