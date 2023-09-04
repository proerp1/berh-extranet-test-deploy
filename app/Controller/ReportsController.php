<?php

App::import('Controller', 'Incomes');
class ReportsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
    public $uses = ['Income', 'Customer', 'OrderItem', 'CostCenter', 'CustomerDepartment', 'Outcome'];

    public $paginate = [
        'OrderItem' => [
            'limit' => 20, 'order' => ['OrderItem.id' => 'desc'],
            'fields' => [
                'Customer.nome_primario',
                'Customer.documento',
                'CustomerUser.name',
                'CustomerDepartment.name',
                'CustomerUserItinerary.unit_price',
                'CustomerUserItinerary.quantity',
                'OrderItem.*',
                'Benefit.code',
                'Supplier.code',
            ],
            'joins' => [
                [
                    'table' => 'orders',
                    'alias' => 'Order',
                    'type' => 'INNER',
                    'conditions' => ['Order.id = OrderItem.order_id'],
                ],
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => ['Customer.id = Order.customer_id'],
                ],
                [
                    'table' => 'customer_users',
                    'alias' => 'CustomerUser',
                    'type' => 'INNER',
                    'conditions' => ['CustomerUser.id = OrderItem.customer_user_id'],
                ],
                [
                    'table' => 'customer_departments',
                    'alias' => 'CustomerDepartment',
                    'type' => 'LEFT',
                    'conditions' => ['CustomerDepartment.id = CustomerUser.customer_departments_id'],
                ],
                [
                    'table' => 'cost_center',
                    'alias' => 'CostCenter',
                    'type' => 'LEFT',
                    'conditions' => ['CostCenter.id = CustomerUser.customer_departments_id'],
                ],
                [
                    'table' => 'customer_user_itineraries',
                    'alias' => 'CustomerUserItinerary',
                    'type' => 'INNER',
                    'conditions' => ['CustomerUserItinerary.id = OrderItem.customer_user_itinerary_id'],
                ],
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => ['Benefit.id = CustomerUserItinerary.benefit_id'],
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => ['Supplier.id = Benefit.supplier_id'],
                ],
            ],
            'recursive' => -1,
        ],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($cliente_id)
    {
        // $this->Permission->check(1, "leitura")? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['Customer.id' => CakeSession::read('Auth.CustomerUser.customer_id')], 'or' => []];

        if (!isset($_GET['de']) && !isset($_GET['para'])) {
            $dates = $this->getCurrentDates();
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $dates['from']]);
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $dates['to']]);

            $de = $dates['from'];
            $para = $dates['to'];
        }

        if (isset($_GET['de']) and $_GET['de'] != '') {
            $deRaw = $_GET['de'];
            $dateObjectDe = DateTime::createFromFormat('d/m/Y', $deRaw);
            $de = $dateObjectDe->format('Y-m-d');
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $de]);
        }

        if (isset($_GET['para']) and $_GET['para'] != '') {
            $paraRaw = $_GET['para'];
            $dateObjectPara = DateTime::createFromFormat('d/m/Y', $paraRaw);
            $para = $dateObjectPara->format('Y-m-d');
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $para]);
        }

        if (isset($_GET['d']) and $_GET['d'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['CustomerDepartment.id' => $_GET['d']]);
        }

        if (isset($_GET['cc']) and $_GET['cc'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['CostCenter.id' => $_GET['cc']]);
        }

        if (isset($_GET['excel'])) {
            $this->paginate['OrderItem'] = $this->ExcelConfiguration->getConfiguration('OrderItem');
            $this->Paginator->settings = $this->paginate;
        }

        $data = $this->Paginator->paginate('OrderItem', $condition);

        $costCenters = $this->CostCenter->find('list', ['fields' => ['id', 'name'], 'conditions' => ['CostCenter.customer_id' => CakeSession::read('Auth.CustomerUser.customer_id')], 'recursive' => -1]);
        $departments = $this->CustomerDepartment->find('list', ['fields' => ['id', 'name'], 'conditions' => ['CustomerDepartment.customer_id' => CakeSession::read('Auth.CustomerUser.customer_id')], 'recursive' => -1]);

        // debug($costCenters);
        // die;

        if (isset($_GET['excel'])) {
            $this->ExcelGenerator->gerarExcelItineraries('itinerarios', $data);

            $this->redirect('/private_files/baixar/excel/itinerarios_xlsx');
        }

        $de = date('d/m/Y', strtotime($de));
        $para = date('d/m/Y', strtotime($para));

        $action = 'Itinerários';
        $breadcrumb = ['Relatórios' => '', 'Itinerários' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'costCenters', 'departments', 'de', 'para'));
    }

    public function baixa_manual()
    {
        $where = '';

        if (!empty($_GET['q'])) {
            $where .= " AND (c.codigo_associado like '%{$_GET['q']}%' or c.nome_secundario like '%{$_GET['q']}%' or u.name like '%{$_GET['q']}%' or i.name like '%{$_GET['q']}%') ";
        }

        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';

        if ($get_de != '' and $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $where .= " AND i.data_baixa BETWEEN '{$de} 00:00:00' AND '{$ate} 23:59:59' ";
        }

        $data = [];
        if ($where != '') {
            $data = $this->Outcome->query("
				SELECT c.codigo_associado, 
							 c.nome_secundario, 
							 i.`name` AS mensalidade, 
							 i.vencimento, 
							 i.data_pagamento, 
							 i.valor_total, 
							 i.valor_pago,
							 i.data_baixa, 
							 u.`name` AS usuarioBaixa
				FROM incomes i
					INNER JOIN customers c ON c.id = i.customer_id
					LEFT JOIN users u ON u.id = i.usuario_id_baixa
				WHERE i.data_cancel = '1901-01-01' 
					AND i.status_id = 17 
					AND i.data_baixa IS NOT NULL 
					{$where}
				ORDER BY i.data_baixa desc
			");
        }

        if (isset($_GET['exportar'])) {
            $nome = 'baixa_manual.xlsx';

            $this->ExcelGenerator->gerarBaixaManual($nome, $data);
            $this->redirect('/files/excel/'.$nome);
        }

        $action = 'Baixa manual';
        $this->set(compact('data', 'action'));
    }

    private function getCurrentDates()
    {
        $currentDate = new DateTime();

        $firstDayOfMonth = new DateTime($currentDate->format('Y-m-01'));

        $to = $currentDate;

        $from = $firstDayOfMonth->format('Y-m-d');
        $to = $to->format('Y-m-d');

        return compact('from', 'to');
    }
}
