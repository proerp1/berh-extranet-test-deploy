<?php

App::import('Controller', 'Incomes');
class ReportsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration', 'CustomReports', 'HtmltoPdf'];
    public $uses = ['Income', 'Customer', 'CustomerUser', 'OrderItem', 'CostCenter', 'CustomerDepartment', 'Outcome', 'Order', 'Status'];

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

        if (isset($_GET['para']) and $_GET['para'] != 'Selecione') {
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

    public function pedidosConditions()
    {
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

        if (isset($_GET['num']) && $_GET['num'] != '') {
            // Dividindo a entrada em uma matriz de números
            $selectedNumbers = preg_split("/[\s,]+/", $_GET['num']);
            
            // Removendo valores em branco da matriz
            $selectedNumbers = array_filter($selectedNumbers, 'strlen');

            // Adicionando a condição para cada número selecionado
            $orConditions = [];
            foreach ($selectedNumbers as $number) {
                $orConditions[] = ['Order.id' => $number];
            }

            // Unindo as condições com OR
            $condition['and'][] = ['or' => $orConditions];
        }


        if (isset($_GET['st']) and $_GET['st'] != '') {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['st']]);
        }

        if (isset($_GET['c']) and $_GET['c'] != 'Selecione') {
            $condition['and'] = array_merge($condition['and'], ['Customer.id' => $_GET['c']]);
        } /*else {
            $condition['and'] = array_merge($condition['and'], ['1 = 2']);
        }*/

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], [
                'CustomerUser.name LIKE' => '%' . $_GET['q'] . '%',
                'CustomerUser.email LIKE' => '%' . $_GET['q'] . '%',
                'CustomerUser.cpf LIKE' => '%' . $_GET['q'] . '%',
                'Customer.nome_primario LIKE' => '%' . $_GET['q'] . '%',
                'Customer.documento LIKE' => '%' . $_GET['q'] . '%',
                'Order.id LIKE' => '%' . $_GET['q'] . '%',
                'Customer.id LIKE' => '%' . $_GET['q'] . '%',
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%",

            ]);
        }

        return compact('condition', 'de', 'para');
    }

    public function pedidos()
    {
        $this->Permission->check(64, "leitura") ? "" : $this->redirect("/not_allowed");
	    ini_set('memory_limit', '-1');

	    set_time_limit(90);
        ini_set('max_execution_time', -1); 

	    
        $paginationConfig = $this->CustomReports->configPagination('pedidos');
        $this->Paginator->settings = $paginationConfig;

        $condition = $this->pedidosConditions();

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

        $data = $this->Paginator->paginate('OrderItem', $condition['condition']);

        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'conditions' => ['Customer.status_id' => 3], 'recursive' => -1]);

        if (isset($_GET['excel'])) {
            $this->ExcelGenerator->gerarExcelOrders('PedidoCompras', $data);

            $this->redirect('/private_files/baixar/excel/PedidoCompras_xlsx');
        }


        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 18]]);

        $de = date('d/m/Y', strtotime($condition['de']));
        $para = date('d/m/Y', strtotime($condition['para']));

        $action = 'Pedidos';
        $breadcrumb = ['Relatórios' => '', 'Pedidos' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'de', 'para', 'customers', 'statuses'));
    }

    public function relatorio_processamento()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        ini_set('memory_limit', '-1');

        // Define conditions similarly to demanda_judicial
        $condition = $this->pedidosConditions();

        // Fetch the data based on the conditions
        $data = $this->OrderItem->getProcessamentoPedido('all', $condition['condition']);

        // Generate the Excel report with the fetched data
        $this->ExcelGenerator->gerarExcelOrdersprocessamento('ProcessamentoPedidoOperadora', $data);

        // Redirect to download the generated Excel file
        $this->redirect('/private_files/baixar/excel/ProcessamentoPedidoOperadora_xlsx');
    }

    
    public function demanda_judicial()
    {
        // Desativar a renderização automática da view
        $this->autoRender = false;
    
        ini_set('pcre.backtrack_limit', '15000000');
        ini_set('memory_limit', '-1');
        $condition = $this->pedidosConditions();
    
        $paginas = $this->OrderItem->find('all', [
            'fields' => ['Order.order_period_from', 'Order.order_period_to', 'Order.id', 'Customer.documento', 'Customer.nome_secundario'],
            'contain' => ['Order', 'CustomerUser'],
            'joins' => [
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => ['Customer.id = Order.customer_id'],
                ],
            ],
            'conditions' => $condition['condition'],
            'group' => ['CustomerUser.id']
        ]);
    
        $html = '';
    
        if (!empty($paginas)) {
            foreach ($paginas as $index => $pagina) {
                $view = new View($this, false);
                $view->layout = false;
    
                $itens = $this->OrderItem->find('all', [
                    'contain' => ['Order', 'CustomerUser', 'CustomerUserItinerary'],
                    'joins' => [
                        [
                            'table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Customer.id = Order.customer_id'],
                        ],
                    ],
                    'fields' => [
                        'Order.created',
                        'OrderItem.subtotal',
                        'Customer.documento',
                        'Customer.nome_secundario',
                        'CustomerUser.name as nome',
                        'CustomerUser.cpf as cpf',
                        'CustomerUser.matricula as matricula',
                        'CustomerUserItinerary.benefit_id as matricula',
                        'Order.credit_release_date',
                        'Order.id',
                        'CustomerUserItinerary.benefit_id',
                        'CustomerUserItinerary.unit_price',
                        'sum(CustomerUserItinerary.quantity) as qtd',
                        'sum(OrderItem.subtotal) as valor',
                        'sum(OrderItem.total) as total',
                        'sum(OrderItem.working_days) as working_days',
                    ],
                    'conditions' => Hash::merge($condition['condition'], ['CustomerUser.id' => $pagina['CustomerUser']['id']]),
                    'group' => ['OrderItem.id'],
                    'order' => ['trim(CustomerUser.name)']                           
                ]);
    
                $de = $condition['de'];
                $para = $condition['para'];
    
                $link = APP . 'webroot';
                $view->set(compact("link","pagina", "itens", "de", "para"));
                $html .= $view->render('../Elements/listagem_entrega');
    
                if (count($paginas) != ($index + 1)) {
                    $html .= '<div class="break"></div>';
                }
            }
    
            // Exibir o HTML antes de gerar o PDF
            //echo $html;
    
            // Para gerar o PDF após visualização, use o seguinte código:
             $this->HtmltoPdf->convert($html, 'demanda_judicial.pdf', 'download');
        } else {
            $this->redirect($this->referer());
        }
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

    public function lgpd()
    {
        $this->Permission->check(64, "leitura") ? "" : $this->redirect("/not_allowed");
        
        $paginationConfig = $this->CustomReports->configPagination('lgpd');
        $this->Paginator->settings = $paginationConfig;

        $condition = ['and' => ['CustomerUser.data_cancel' => '1901-01-01 00:00:00', 'CustomerUser.flag_lgpd != ' => 0], 'or' => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], [
                'CustomerUser.name LIKE' => '%' . $_GET['q'] . '%',
                'CustomerUser.cpf LIKE' => '%' . $_GET['q'] . '%'
            ]);
        }

        $data = $this->Paginator->paginate('CustomerUser', $condition);

        $action = 'LGPD';
        $breadcrumb = ['Relatórios' => '', 'LGPD' => ''];

        $this->set(compact('data', 'action', 'breadcrumb'));
    }

    public function extrato($tipo = null)
    {
        $this->Permission->check(64, "leitura") ? "" : $this->redirect("/not_allowed");
        
        $id = null;
        if (isset($_GET['c'])) {
            $id = $_GET['c'];
        }

        $this->Customer->id = $id;
        $cliente = $this->Customer->read();

        if ($tipo == 'grupo_economico') {
            $this->Paginator->settings = [
                'Order' => [
                    'fields' => [
                        'Order.*',
                        'Income.*',
                        'Status.*',
                        'Creator.*',
                        'CustomerCreator.*',
                        'EconomicGroup.*',
                        "(SELECT coalesce(sum(b.total), 0) as total_balances 
                            FROM order_balances b 
                                INNER JOIN orders o ON o.id = b.order_id 
                            WHERE o.id = Order.id 
                                    AND b.tipo = 1 
                                    AND b.data_cancel = '1901-01-01 00:00:00' 
                                    AND o.data_cancel = '1901-01-01 00:00:00' 
                        ) as total_balances"
                    ],
                    'limit' => 25,
                    'group' => 'EconomicGroup.id',
                    'order' => ['Order.created' => 'asc'],
                ]
            ];
            
            $condition = ['and' => ['Customer.id' => $id, 'EconomicGroup.id != ' => null], 'or' => []];
        } else {
            $this->Paginator->settings = [
                'Order' => [
                    'fields' => [
                        'Order.*',
                        'Income.*',
                        'Status.*',
                        'Creator.*',
                        'CustomerCreator.*',
                        'EconomicGroup.*',
                        "(SELECT coalesce(sum(b.total), 0) as total_balances 
                            FROM order_balances b 
                                INNER JOIN orders o ON o.id = b.order_id 
                            WHERE o.id = Order.id 
                                    AND b.tipo = 1 
                                    AND b.data_cancel = '1901-01-01 00:00:00' 
                                    AND o.data_cancel = '1901-01-01 00:00:00' 
                        ) as total_balances"
                    ],
                    'limit' => 25,
                    'order' => ['Order.created' => 'asc'],
                ]
            ];

            $condition = ['and' => ['Customer.id' => $id], 'or' => []];
        }
        
        $data = [];
        $saldo = 0;
    
        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%", 
            ]);
        }
    
        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], [
                'Order.status_id' => $_GET['t']
            ]);
        }
    
        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';
    
        if ($get_de != '' and $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));
    
            $condition['and'] = array_merge($condition['and'], [
                'Order.created between ? and ?' => [$de . ' 00:00:00', $ate . ' 23:59:59']
            ]);
            
            $data = $this->Paginator->paginate('Order', $condition);

            $de_anterior = date('Y-m-d', strtotime('-1 day '.$de));

            $orderDesconto = $this->Order->find('all', ['conditions' => ['Order.customer_id' => $id, "Order.created <= '{$de_anterior}'"], 'fields' => 'SUM(Order.desconto) as valor_desconto']);
            $orderSaldo = $this->Order->find('all', ['conditions' => ['Order.customer_id' => $id, "Order.created <= '{$de_anterior}'"], 'fields' => 'SUM(Order.saldo) as valor_saldo']);

            $saldo = ($orderSaldo[0][0]['valor_saldo'] - $orderDesconto[0][0]['valor_desconto']);

            if (isset($cliente['Customer']['dt_economia_inicial_nao_formatado'])) {
                if ($cliente['Customer']['dt_economia_inicial_nao_formatado'] <= $de_anterior) {
                    $saldo = $cliente['Customer']['economia_inicial_not_formated'];
                }
            }
        }

        $first_order = $this->Order->find('first', ['conditions' => ['Order.customer_id' => $id], 'fields' => 'MIN(Order.created) as data_criacao']);

        $totalOrders = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'fields' => [
                'sum(Order.subtotal) as subtotal',
                'sum(Order.transfer_fee) as transfer_fee',
                'sum(Order.commission_fee) as commission_fee',
                'sum(Order.desconto) as desconto',
                'sum(Order.saldo) as saldo',
                'sum(Order.total) as total',
                'sum(Order.total_saldo) as total_saldo',
                "(SELECT coalesce(sum(b.total), 0) as total_balances 
                    FROM order_balances b 
                        INNER JOIN orders o ON o.id = b.order_id 
                    WHERE o.customer_id = Customer.id 
                            AND b.tipo = 1 
                            AND b.data_cancel = '1901-01-01 00:00:00' 
                            AND o.data_cancel = '1901-01-01 00:00:00' 
                ) as total_balances"
            ],
            'conditions' => $condition,
            'recursive' => -1
        ]);
    
        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'conditions' => ['Customer.status_id' => 3], 'recursive' => -1]);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

        $action = 'Extrato';
        $breadcrumb = ['Relatórios' => '', 'Extrato' => ''];

        $this->set(compact('id', 'data', 'status' ,'action', 'breadcrumb', 'totalOrders', 'saldo', 'first_order', 'tipo', 'customers'));
    }

    public function robos($menu)
    {
        $this->Permission->check(68, "leitura") ? "" : $this->redirect("/not_allowed");

        $url_cookie = 'https://robo.berh.com.br/set-cookie?hash=6eb0fed6ec2700a0ecabe9752644c8d4b43942f6f0193a6b6da7babef9e56841';

        $url_iframe = "";
        if ($menu == 'roteirizacao') {
            $url_iframe = "https://robo.berh.com.br/roteirizacao";
        } elseif ($menu == 'extratos') {
            $url_iframe = "https://robo.berh.com.br/extratos";
        } elseif ($menu == 'consulta_transurc') {
            $url_iframe = "https://robo.berh.com.br/transurc";
        } elseif ($menu == 'consulta_sptrans') {
            $url_iframe = "https://robo.berh.com.br/sptrans";
        } elseif ($menu == 'captura_boletos') {
            $url_iframe = "https://robo.berh.com.br/captura_boletos";
        } elseif ($menu == 'conversor_layouts') {
            $url_iframe = "https://robo.berh.com.br/conversor_layouts";
        }

        $this->set("action", "Robô - Roteirização");
        $this->set(compact("url_cookie", "url_iframe"));
    }

    public function nfs()
    {
        ini_set('pcre.backtrack_limit', '15000000');

        $this->Permission->check(69, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = [
                'Order' => [
                    'contain' => ['Customer', 'CustomerCreator', 'EconomicGroup', 'Status', 'Creator', 'Income'],
                    'fields' => [
                        'Order.*',
                        'OrderDocument.*',
                        'Status.id',
                        'Status.label',
                        'Status.name',
                        'Customer.codigo_associado',
                        'CustomerCreator.name',
                        'Creator.name',
                        'EconomicGroup.name',
                        'Customer.nome_primario',
                        'Income.data_pagamento',
                        "(SELECT coalesce(sum(b.total), 0) as total_balances 
                            FROM order_balances b 
                                INNER JOIN orders o ON o.id = b.order_id 
                            WHERE o.id = Order.id 
                                    AND b.tipo = 1 
                                    AND b.data_cancel = '1901-01-01 00:00:00' 
                                    AND o.data_cancel = '1901-01-01 00:00:00' 
                        ) as total_balances"
                    ],
                    'joins' => [
                        [
                            'table' => 'order_documents',
                            'alias' => 'OrderDocument',
                            'type' => 'INNER',
                            'conditions' => [
                                'Order.id = OrderDocument.order_id', "OrderDocument.data_cancel = '1901-01-01 00:00:00'"
                            ]
                        ],
                    ],
                    'limit' => 50, 
                    'order' => ['Order.id' => 'desc']
                ],
            ];

        ini_set('memory_limit', '-1');

        $condition = ["and" => [], "or" => []];
        $filtersFilled = false;
    
        if (isset($_GET['q']) && $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'Order.id' => $_GET['q'], 
                'Customer.nome_primario LIKE' => "%" . $_GET['q'] . "%", 
                'EconomicGroup.name LIKE' => "%" . $_GET['q'] . "%", 
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%", 
                'Customer.id LIKE' => "%" . $_GET['q'] . "%"
            ]);
            $filtersFilled = true;
        }
    
        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['t']]);
            $filtersFilled = true;
        }
    
        $get_de = isset($_GET['de']) ? $_GET['de'] : '';
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : '';
    
        if ($get_de != '' && $get_ate != '') {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));
    
            $condition['and'] = array_merge($condition['and'], [
                'Order.created between ? and ?' => [$de . ' 00:00:00', $ate . ' 23:59:59']
            ]);
            $filtersFilled = true;
        }
    
        $get_de_pagamento = isset($_GET['de_pagamento']) ? $_GET['de_pagamento'] : '';
        $get_ate_pagamento = isset($_GET['ate_pagamento']) ? $_GET['ate_pagamento'] : '';
    
        if ($get_de_pagamento != '' && $get_ate_pagamento != '') {
            $de_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_de_pagamento)));
            $ate_pagamento = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate_pagamento)));
    
            $condition['and'] = array_merge($condition['and'], [
                'Income.data_pagamento between ? and ?' => [$de_pagamento . ' 00:00:00', $ate_pagamento . ' 23:59:59']
            ]);
            $filtersFilled = true;
        }
    
        $queryString = http_build_query($_GET);
    
        if (isset($_GET['exportar'])) {
            $nome = 'pedidos_nfs_' . date('d_m_Y_H_i_s') . '.xlsx';
    
            $data = $this->Order->find('all', [
                'contain' => ['Customer', 'CustomerCreator', 'EconomicGroup', 'Status', 'Creator', 'Income'],
                'fields' => [
                    'Order.*',
                    'OrderDocument.*',
                    'Status.id',
                    'Status.label',
                    'Status.name',
                    'Customer.codigo_associado',
                    'CustomerCreator.name',
                    'Creator.name',
                    'EconomicGroup.name',
                    'Customer.nome_primario',
                    'Income.data_pagamento',
                    "(SELECT coalesce(sum(b.total), 0) as total_balances 
                        FROM order_balances b 
                            INNER JOIN orders o ON o.id = b.order_id 
                        WHERE o.id = Order.id 
                                AND b.tipo = 1 
                                AND b.data_cancel = '1901-01-01 00:00:00' 
                                AND o.data_cancel = '1901-01-01 00:00:00' 
                    ) as total_balances"
                ],
                'joins' => [
                    [
                        'table' => 'order_documents',
                        'alias' => 'OrderDocument',
                        'type' => 'INNER',
                        'conditions' => [
                            'Order.id = OrderDocument.order_id', "OrderDocument.data_cancel = '1901-01-01 00:00:00'"
                        ]
                    ],
                ],
                'conditions' => $condition,
                'order' => ['Order.id' => 'desc']
            ]);
    
            $this->ExcelGenerator->gerarExcelPedidosNfs($nome, $data);
    
            $this->redirect("/files/excel/" . $nome);
        }
    
        $data = $this->Paginator->paginate('Order', $condition);

        $customers = $this->Customer->find('list', [
            'conditions' => ['Customer.status_id' => 3],
            'fields' => ['id', 'nome_primario'],
            'order' => ['nome_primario' => 'asc']
        ]);
    
        $totalOrders = $this->Order->find('first', [
            'contain' => ['Customer', 'EconomicGroup', 'Income'],
            'fields' => [
                'sum(Order.subtotal) as subtotal',
                'sum(Order.transfer_fee) as transfer_fee',
                'sum(Order.commission_fee) as commission_fee',
                'sum(Order.desconto) as desconto',
                'sum(Order.total) as total',
            ],
            'conditions' => $condition,
            'recursive' => -1
        ]);
    
        $benefit_types = [-1 => 'Transporte', 4 => 'PAT', 999 => 'Outros'];
    
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
    
        $action = 'Notas Fiscais Emitidas';
        $breadcrumb = ['Cadastros' => '', 'Notas Fiscais Emitidas' => ''];
        $this->set(compact('data', 'status' ,'action', 'breadcrumb', 'customers', 'benefit_types', 'totalOrders', 'filtersFilled', 'queryString'));
    }
}
