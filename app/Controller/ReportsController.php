<?php

App::import('Controller', 'Incomes');
class ReportsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration', 'CustomReports'];
    public $uses = ['Income', 'Customer', 'OrderItem', 'CostCenter', 'CustomerDepartment', 'Outcome', 'Order', 'Status'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(64, "leitura") ? "" : $this->redirect("/not_allowed");

        if (isset($_GET['tp'])) {
            $paginationConfig = $this->CustomReports->configPagination($_GET['tp']);
        } else {
            $paginationConfig = $this->CustomReports->configPagination('default');
        }
        $this->Paginator->settings = $paginationConfig;

        $condition = ['and' => ['Order.data_cancel' => '1901-01-01 00:00:00'], 'or' => []];

        if (!isset($_GET['de']) && !isset($_GET['para'])) {
            $dates = $this->getCurrentDates();
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $dates['from']]);
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $dates['to'] . ' 23:59:59']);

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
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $para . ' 23:59:59']);
        }

        if (isset($_GET['sup']) and $_GET['sup'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['Supplier.id' => $_GET['sup']]);
        }

        if (isset($_GET['st']) and $_GET['st'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['st']]);
        }

        if (isset($_GET['c']) and $_GET['c'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['Customer.id' => $_GET['c']]);
        } else {
            $condition['and'] = array_merge($condition['and'], ['1 = 2']);
        }

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], [
                'CustomerUser.name LIKE' => '%' . $_GET['q'] . '%',
                'CustomerUser.email LIKE' => '%' . $_GET['q'] . '%',
                'CustomerUser.cpf LIKE' => '%' . $_GET['q'] . '%',
                'Customer.nome_primario LIKE' => '%' . $_GET['q'] . '%',
                'Customer.documento LIKE' => '%' . $_GET['q'] . '%',
            ]);
        }

        if (isset($_GET['excel'])) {
            $pag = $this->ExcelConfiguration->getConfiguration('OrderItem');
            $this->Paginator->settings = ['OrderItem' => $pag];
        }

        $data = $this->Paginator->paginate('OrderItem', $condition);

        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'conditions' => ['Customer.status_id' => 3], 'recursive' => -1]);

        if (isset($_GET['excel'])) {
            $this->ExcelGenerator->gerarExcelItineraries('itinerarios_admin', $data);

            $this->redirect('/private_files/baixar/excel/itinerarios-admin_xlsx');
        }

        $de = date('d/m/Y', strtotime($de));
        $para = date('d/m/Y', strtotime($para));

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 18]]);

        $action = 'Itinerários';
        $breadcrumb = ['Relatórios' => '', 'Itinerários' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'de', 'para', 'customers', 'statuses'));
    }

    public function pedidos()
    {
        $this->Permission->check(64, "leitura") ? "" : $this->redirect("/not_allowed");

        $paginationConfig = $this->CustomReports->configPagination('pedidos');
        $this->Paginator->settings = $paginationConfig;

        $condition = ['and' => ['Order.data_cancel' => '1901-01-01 00:00:00'], 'or' => []];

        if (!isset($_GET['de']) && !isset($_GET['para'])) {
            $dates = $this->getCurrentDates();
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $dates['from']]);
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $dates['to'] . ' 23:59:59']);

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
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $para . ' 23:59:59']);
        }

        if (isset($_GET['sup']) and $_GET['sup'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['Supplier.id' => $_GET['sup']]);
        }

        if (isset($_GET['st']) and $_GET['st'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['st']]);
        }

        if (isset($_GET['c']) and $_GET['c'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['Customer.id' => $_GET['c']]);
        } else {
            $condition['and'] = array_merge($condition['and'], ['1 = 2']);
        }

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], [
                'CustomerUser.name LIKE' => '%' . $_GET['q'] . '%',
                'CustomerUser.email LIKE' => '%' . $_GET['q'] . '%',
                'CustomerUser.cpf LIKE' => '%' . $_GET['q'] . '%',
                'Customer.nome_primario LIKE' => '%' . $_GET['q'] . '%',
                'Customer.documento LIKE' => '%' . $_GET['q'] . '%',
            ]);
        }

        if (isset($_GET['excel'])) {
            $pag = $this->ExcelConfiguration->getConfiguration('OrderItemReportsPedido');
            $this->Paginator->settings = ['OrderItem' => $pag];
        }

        if (isset($_GET['o'])) {
            $order_field = [
                'nome' => 'CustomerUser.name',
                'cpf' => 'CustomerUser.cpf',
                'dep' => 'CustomerDepartment.name',
                'cod_o' => 'Supplier.code',
                'cod_b' => 'Benefit.code',
                'val_u' => 'CustomerUserItinerary.unit_price',
                'qty' => 'CustomerUserItinerary.quantity',
                'wd' => 'OrderItem.working_days',
                'var' => 'OrderItem.var',
                'total' => 'OrderItem.subtotal',
            ];

            $order = $order_field[$_GET['o']];
            $dir = isset($_GET['dir']) ? $_GET['dir'] : 'u';
            $direction = $dir == 'u' ? 'asc' : 'desc';

            $this->Paginator->settings['OrderItem']['order'] = $order . ' ' . $direction;
        }

        $data = $this->Paginator->paginate('OrderItem', $condition);

        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'conditions' => ['Customer.status_id' => 3], 'recursive' => -1]);

        if (isset($_GET['excel'])) {
            $this->ExcelGenerator->gerarExcelOrders('PedidoCompras', $data);

            $this->redirect('/private_files/baixar/excel/PedidoCompras_xlsx');
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 18]]);

        $de = date('d/m/Y', strtotime($de));
        $para = date('d/m/Y', strtotime($para));

        $action = 'Pedidos';
        $breadcrumb = ['Relatórios' => '', 'Pedidos' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'de', 'para', 'customers', 'statuses'));
    }

    public function getDepAndCCByCustomer()
    {
        $this->autoRender = false;

        $customer_id = $_POST['customer_id'];

        $departments = $this->CustomerDepartment->find('all', ['fields' => ['id', 'name'], 'conditions' => ['CustomerDepartment.customer_id' => $customer_id], 'recursive' => -1]);
        $costCenters = $this->CostCenter->find('all', ['fields' => ['id', 'name'], 'conditions' => ['CostCenter.customer_id' => $customer_id], 'recursive' => -1]);

        echo json_encode(['departments' => $departments, 'costCenters' => $costCenters]);
    }

    public function getSupplierAndCustomerByDate()
    {
        $this->autoRender = false;

        // convert brazilian date to mysql date
        $ini = $this->Order->date_converter($_POST['ini']);
        $end = $this->Order->date_converter($_POST['end']);

        if($ini == '' && $end == '') {
            echo json_encode(['suppliers' => [], 'customers' => []]);
            return;
        }

        if($ini != '' && $end == ''){
            $cond = ['OrderItem.created > ?' => [$ini]];
        }

        if($ini == '' && $end != ''){
            $cond = ['OrderItem.created < ?' => [$end]];
        }

        if($ini != '' && $end != ''){
            $cond = ['OrderItem.created BETWEEN ? AND ?' => [$ini, $end]];
        }

        $suppliers = $this->OrderItem->find('all',
            [
                'fields' => ['Supplier.id', 'Supplier.nome_fantasia'],
                'conditions' => $cond,
                'group' => ['Supplier.id'],
                'recursive' => -1,
                'joins' => [
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
                    ]
                ]
            ]
        );

        $customers = $this->OrderItem->find('all',
            [
                'fields' => ['Customer.id', 'Customer.nome_primario'],
                'conditions' => $cond,
                'group' => ['Customer.id'],
                'recursive' => -1,
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
                    ]
                ]
            ]
        );
        

        echo json_encode(['suppliers' => $suppliers, 'customers' => $customers]);
    }

    public function baixa_manual()
    {
        $where = '';

        if (!empty($_GET['q'])) {
            $where .= " AND (c.documento like '%{$_GET['q']}%' or c.nome_secundario like '%{$_GET['q']}%' or u.name like '%{$_GET['q']}%' or i.name like '%{$_GET['q']}%') ";
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
				SELECT c.documento, 
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
            $this->redirect('/files/excel/' . $nome);
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
