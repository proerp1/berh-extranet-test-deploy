<?php
class CustomerConsumoDiarioController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Customer', 'ConsumoDiarioItem'];

    public $paginate = [
        'ConsumoDiarioItem' => [
            'fields' => ['count(ConsumoDiarioItem.id) as qtde', 'ConsumoDiarioItem.data', 'Customer.nome_primario', 'Customer.codigo_associado', 'Customer.id'],
            'limit' => 10, 
            'order' => ['ConsumoDiarioItem.data' => 'asc'],
            'group' => ['ConsumoDiarioItem.data']
        ],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Permission->check(3, "leitura")? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Customer.id' => $id], "or" => []];

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        $data = $this->Paginator->paginate('ConsumoDiarioItem', $condition);

        $action = 'Consumo diário';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Consumo diário' => ''
        ];
        $this->set(compact('data', 'action', 'id', 'breadcrumb'));
    }
}
