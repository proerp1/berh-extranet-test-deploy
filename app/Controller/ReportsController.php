<?php

App::import('Controller', 'Incomes');
class ReportsController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration', 'CustomReports', 'HtmltoPdf'];
    public $uses = ['Income', 'Customer', 'CustomerUser', 'OrderItem', 'CostCenter', 'CustomerDepartment', 'Outcome', 'Order', 'Status', 'OrderBalanceFile', 'Log', 'OrderBalance'];

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

            $condition['and'] = array_merge($condition['and'], ['OrderItem.created between ? and ?' => [$dates['from'], $dates['to'] . ' 23:59:59']]);

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

        if (!empty($_GET['stp'])) {
            $condition['and'] = array_merge($condition['and'], ['OrderItem.status_processamento' => $_GET['stp']]);
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

// Mapeamento dos IDs para nomes de benefícios
$benefitTypes = [1 => 'Cartão',2 => 'Papel',3 => 'Passe Comum',4 => 'PAT',5 => 'Beneflex',6 => 'Saúde',7 => 'Cultura',8 => 'Combustível',9 => 'Frota',10 => 'Premiação',];

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

        $data = [];
        if (!empty($_GET)) {
            $data = $this->Paginator->paginate('OrderItem', $condition['condition']);
        }

        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'conditions' => ['Customer.status_id' => 3], 'recursive' => -1]);

        if (isset($_GET['excel'])) {
            $nome = 'relatorio_pedidos_' . date('d_m_Y_H_i_s') . '.xlsx';

            $this->ExcelGenerator->gerarExcelOrders($nome, $data);
            $this->redirect('/files/excel/' . $nome);
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
                    ],
                    'limit' => 25,
                    'group' => 'EconomicGroup.id',
                    'order' => ['Order.created' => 'asc'],
                ]
            ];
            
            $condition = ['and' => ['Order.customer_id' => $id, 'EconomicGroup.id != ' => null], 'or' => []];
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
                    ],
                    'limit' => 25,
                    'order' => ['Order.created' => 'asc'],
                ]
            ];

            $condition = ['and' => ['Order.customer_id' => $id], 'or' => []];
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
        

            $first_order = $this->Order->find('first', ['conditions' => ['Order.customer_id' => $id], 'fields' => 'MIN(Order.created) as data_criacao']);

            $totalOrders = $this->Order->find('first', [
                'contain' => ['Customer', 'EconomicGroup', 'Income'],
                'fields' => [
                    'count(1) as qtde_pedidos',
                    "IFNULL(
                        (SELECT COUNT(1)
                            FROM (
                                SELECT COUNT(1), o.customer_id 
                                    FROM orders o 
                                        INNER JOIN order_items i ON i.order_id = o.id 
                                        INNER JOIN customer_users c ON c.id = i.customer_user_id 
                                    WHERE i.data_cancel = '1901-01-01 00:00:00' 
                                            AND o.data_cancel = '1901-01-01 00:00:00' 
                                    GROUP BY c.cpf, o.customer_id 
                            ) rw 
                            WHERE rw.customer_id = Customer.id 
                        ), 
                    0) as qtde_order_customers",
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
                    ) as total_balances",
                    'sum(Order.tpp_fee) as vl_tpp',
                ],
                'conditions' => $condition,
                'recursive' => -1
            ]);

            $data_orders = $this->Order->find('all', [
                'contain' => ['Customer', 'EconomicGroup', 'Income'],
                'fields' => [
                    'Order.id',
                ],
                'conditions' => $condition,
                'order' => ['Order.created' => 'asc'],
                'recursive' => -1
            ]);
            
            $total_fee_economia         = 0;
            $total_vl_economia          = 0;
            $total_repasse_economia     = 0;
            $total_diferenca_repasse    = 0;
            $total_bal_ajuste_cred      = 0;
            $total_bal_ajuste_deb       = 0;
            $total_bal_inconsistencia   = 0;
            $total_vlca                 = 0;
            
            if ($data_orders) {
                for ($i = 0; $i < count($data_orders); $i++) {
                    $data_extrato = $this->Order->getExtrato($data_orders[$i]["Order"]["id"]);

                    $total_fee_economia         += $data_extrato['v_fee_economia'];
                    $total_vl_economia          += $data_extrato['v_vl_economia'];
                    $total_repasse_economia     += $data_extrato['v_repasse_economia'];
                    $total_diferenca_repasse    += $data_extrato['v_diferenca_repasse'];
                    $total_bal_ajuste_cred      += $data_extrato['v_total_bal_ajuste_cred'];
                    $total_bal_ajuste_deb       += $data_extrato['v_total_bal_ajuste_deb'];
                    $total_bal_inconsistencia   += $data_extrato['v_total_bal_inconsistencia'];
                    $total_vlca                 += $data_extrato['v_total_vlca'];
                }
            }

            foreach ($data as &$item) {
                $item['Order']['extrato'] = $this->Order->getExtrato($item['Order']['id']);
            }

            unset($item);

        }
        
        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'conditions' => ['Customer.status_id' => 3], 'recursive' => -1]);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

        $action = 'Extrato';
        $breadcrumb = ['Relatórios' => '', 'Extrato' => ''];

        $this->set(compact('id', 'data', 'customers', 'status' ,'action', 'breadcrumb', 'totalOrders', 'saldo', 'first_order', 'tipo'));
        $this->set(compact('total_fee_economia', 'total_vl_economia', 'total_repasse_economia', 'total_diferenca_repasse', 'total_bal_ajuste_cred', 'total_bal_ajuste_deb', 'total_bal_inconsistencia', 'total_vlca'));
    }

    public function robos($menu)
    {
        $url_cookie = 'https://robo.berh.com.br/set-cookie?hash=6eb0fed6ec2700a0ecabe9752644c8d4b43942f6f0193a6b6da7babef9e56841';

        $url_iframe = "";
        if ($menu == 'roteirizacao') {
	    $this->Permission->check(68, "leitura") ? "" : $this->redirect("/not_allowed");
            $breadcrumb='Roteirização';
            $url_iframe = "https://robo.berh.com.br/roteirizacao";
        } elseif ($menu == 'extratos') {
	    $this->Permission->check(72, "leitura") ? "" : $this->redirect("/not_allowed");
            $breadcrumb='Extratos';
            $url_iframe = "https://robo.berh.com.br/extratos";
        } elseif ($menu == 'consulta_transurc') {
	    $this->Permission->check(68, "leitura") ? "" : $this->redirect("/not_allowed");
            $url_iframe = "https://robo.berh.com.br/transurc";
            $breadcrumb='Transurc';
        } elseif ($menu == 'consulta_sptrans') {
	    $this->Permission->check(73, "leitura") ? "" : $this->redirect("/not_allowed");
            $url_iframe = "https://robo.berh.com.br/sptrans";
            $breadcrumb='Consulta SPTrans';
        } elseif ($menu == 'captura_boletos') {
            $this->Permission->check(74, "leitura") ? "" : $this->redirect("/not_allowed");
            $url_iframe = "https://robo.berh.com.br/captura_boletos";
            $breadcrumb='Captura de boletos';
        } elseif ($menu == 'conversor_layouts') {
        $this->Permission->check(75, "leitura") ? "" : $this->redirect("/not_allowed");
            $url_iframe = "https://robo.berh.com.br/conversor_layouts";
            $breadcrumb='Conversor de layouts';
        } elseif ($menu == 'conversor_compras') {
        $this->Permission->check(75, "leitura") ? "" : $this->redirect("/not_allowed");
            $url_iframe = "https://robo.berh.com.br/conversor_compras";
            $breadcrumb='Conversor de compras';
        }

        $this->set("action", $breadcrumb);
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
                        'Customer.emitir_nota_fiscal',
                        'CustomerCreator.name',
                        'Creator.name',
                        'EconomicGroup.name',
                        'Customer.nome_primario',
                        'Income.data_pagamento',
                        'Income.status_id',
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
                'Customer.codigo_associado LIKE' => "%" . $_GET['q'] . "%", 
                'Customer.id LIKE' => "%" . $_GET['q'] . "%", 
            ]);
            $filtersFilled = true;
        }
    
        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['t']]);
            $filtersFilled = true;
        }

        if (!empty($_GET['antecipada']) && $_GET['antecipada'] != '') {
            $comparator = $_GET['antecipada'] == 'S' ? '=' : '!=';
            $condition['and'] = array_merge($condition['and'], ["Customer.emitir_nota_fiscal $comparator 'A'"]);
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
                    'Customer.emitir_nota_fiscal',
                    'CustomerCreator.name',
                    'Creator.name',
                    'EconomicGroup.name',
                    'Customer.nome_primario',
                    'Income.data_pagamento',
                    'Income.status_id',
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
        $breadcrumb = ['Relatórios' => '', 'Notas Fiscais Emitidas' => ''];
        $this->set(compact('data', 'status' ,'action', 'breadcrumb', 'customers', 'benefit_types', 'totalOrders', 'filtersFilled', 'queryString'));
    }

    public function compras()
    {
        ini_set('memory_limit', '-1');

        $this->Permission->check(70, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Paginator->settings = ['OrderItem' => [
            'limit' => 200,
            'order' => ['Order.id' => 'desc'],
            'fields' => [
                'OrderItem.*',
                'Order.id',
                'Order.working_days',
                'Order.created',
                'Status.label',
                'Status.name',
                'Customer.codigo_associado',
                'Customer.nome_primario',
                'Supplier.nome_fantasia',
                'CustomerUser.name',
                'Benefit.name',
                'CustomerUserItinerary.quantity',
            ],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id', 'Benefit.data_cancel' => '1901-01-01',
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id', 'Supplier.data_cancel' => '1901-01-01',
                    ]
                ],
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => [
                        'Customer.id = Order.customer_id', 'Customer.data_cancel' => '1901-01-01',
                    ],
                ],
                [
                    'table' => 'statuses',
                    'alias' => 'Status',
                    'type' => 'INNER',
                    'conditions' => [
                        'Status.id = Order.status_id',
                    ]
                ],
            ]
        ]];

        $condition = ['and' => ['Order.data_cancel' => '1901-01-01 00:00:00'], 'or' => []];
        
        $buscar = false;
        $de = null;
        $para = null;
        if (isset($_GET['de']) and $_GET['de'] != '') {
            $buscar = true;

            $deRaw = $_GET['de'];
            $dateObjectDe = DateTime::createFromFormat('d/m/Y', $deRaw);
            $de = $dateObjectDe->format('Y-m-d');
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $de]);

            $de = date('d/m/Y', strtotime($de));
        } else {
            $de = date("01/m/Y");
        }

        if (isset($_GET['para']) and $_GET['para'] != '') {
            $buscar = true;

            $paraRaw = $_GET['para'];
            $dateObjectPara = DateTime::createFromFormat('d/m/Y', $paraRaw);
            $para = $dateObjectPara->format('Y-m-d');
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $para . ' 23:59:59']);

            $para = date('d/m/Y', strtotime($para));
        } else {
            $para = date("d/m/Y");
        }

        if (isset($_GET['sup']) and $_GET['sup'] != 'Selecione') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Supplier.id' => $_GET['sup']]);
        }

        if (isset($_GET['num']) && $_GET['num'] != '') {
            $buscar = true;

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
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $_GET['st']]);
        }

        if (isset($_GET['c']) and $_GET['c'] != 'Selecione') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Customer.id' => $_GET['c']]);
        }

        if (!empty($_GET['q'])) {
            $buscar = true;

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
                if (isset($_GET['stp']) and $_GET['stp'] != '') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['OrderItem.status_processamento' => $_GET['stp']]);
        }

        if (isset($_GET['excel'])) {
            $nome = 'relatorio_compras_' . date('d_m_Y_H_i_s') . '.xlsx';

            $data = $this->OrderItem->find('all', [
                'fields' => [
                    'OrderItem.*',
                    'Order.id',
                    'Order.pedido_complementar',
                    'Order.working_days',
                    'Order.created',
                    'Status.label',
                    'Status.name',
                    'Customer.codigo_associado',
                    'Customer.nome_primario',
                    'Supplier.nome_fantasia',
                    'CustomerUser.name',
                    'CustomerUser.cpf',
                    'Benefit.name',
                    'CustomerUserItinerary.quantity',
                    'CustomerUserItinerary.*',

                ],
                'joins' => [
                    [
                        'table' => 'benefits',
                        'alias' => 'Benefit',
                        'type' => 'INNER',
                        'conditions' => [
                            'Benefit.id = CustomerUserItinerary.benefit_id', 'Benefit.data_cancel' => '1901-01-01',
                        ]
                    ],
                    [
                        'table' => 'suppliers',
                        'alias' => 'Supplier',
                        'type' => 'INNER',
                        'conditions' => [
                            'Supplier.id = Benefit.supplier_id', 'Supplier.data_cancel' => '1901-01-01',
                        ]
                    ],
                    [
                        'table' => 'customers',
                        'alias' => 'Customer',
                        'type' => 'INNER',
                        'conditions' => [
                            'Customer.id = Order.customer_id', 'Customer.data_cancel' => '1901-01-01',
                        ],
                    ],
                    [
                        'table' => 'statuses',
                        'alias' => 'Status',
                        'type' => 'INNER',
                        'conditions' => [
                            'Status.id = Order.status_id',
                        ]
                    ],
                ],
                'conditions' => $condition,
            ]);

            $this->ExcelGenerator->gerarRelatorioCompras($nome, $data);
            $this->redirect('/files/excel/' . $nome);
        }

        $items = [];
        $items_total = null;
        if ($buscar) {
            $items = $this->Paginator->paginate('OrderItem', $condition);

            $items_total = $this->OrderItem->find('all', [
                'fields' => ['SUM(OrderItem.subtotal) as subtotal', 'SUM(OrderItem.transfer_fee) as transfer_fee', 'SUM(OrderItem.commission_fee) as commission_fee', 'SUM(OrderItem.total) as total', 'SUM(OrderItem.saldo) as saldo'],
                'joins' => [
                    [
                        'table' => 'benefits',
                        'alias' => 'Benefit',
                        'type' => 'INNER',
                        'conditions' => [
                            'Benefit.id = CustomerUserItinerary.benefit_id'
                        ]
                    ],
                    [
                        'table' => 'suppliers',
                        'alias' => 'Supplier',
                        'type' => 'INNER',
                        'conditions' => [
                            'Supplier.id = Benefit.supplier_id'
                        ]
                    ],
                    [
                        'table' => 'customers',
                        'alias' => 'Customer',
                        'type' => 'INNER',
                        'conditions' => [
                            'Customer.id = Order.customer_id', 'Customer.data_cancel' => '1901-01-01',
                        ],
                    ],
                    [
                        'table' => 'statuses',
                        'alias' => 'Status',
                        'type' => 'INNER',
                        'conditions' => [
                            'Status.id = Order.status_id',
                        ]
                    ],

                ],
                'conditions' => $condition,
            ]);
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 18]]);

        $action = 'Relatório de Compras';
        $breadcrumb = ['Relatórios' => '', 'Relatório de Compras' => ''];

        $this->set(compact('action', 'breadcrumb', 'items', 'statuses', 'buscar', 'items_total', 'de', 'para'));
    }

    public function getSupplierAndCustomer()
    {
        $this->autoRender = false;

        $cond = [];

        $suppliers = $this->OrderItem->find('all',
            [
                'fields' => ['Supplier.id', 'Supplier.nome_fantasia'],
                'order' => ['Supplier.nome_fantasia'],
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
                'order' => ['Customer.nome_primario'],
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

    public function importar_movimentacao()
    {
        ini_set('memory_limit', '-1');

        $this->Permission->check(76, "leitura") ? "" : $this->redirect("/not_allowed");

        $this->Paginator->settings = ['OrderBalanceFile' => [
            'limit' => 200,
            'order' => ['OrderBalanceFile.created' => 'desc'],
        ]];

        $buscar = false;

        $condition = ["and" => [], "or" => []];

        if (!empty($_GET['q'])) {
            $buscar = true;

            $condition['or'] = array_merge($condition['or'], [
                'OrderBalanceFile.file_name LIKE' => "%" . $_GET['q'] . "%", 
            ]);
        }

        $data = $this->Paginator->paginate('OrderBalanceFile', $condition);
        
        $action = 'Relatório de Movimentações';
        $breadcrumb = ['Relatórios' => '', 'Relatório de Movimentações' => ''];

        $this->set(compact('action', 'breadcrumb', 'data', 'buscar'));
    }

    public function alter_item_status_processamento_all()
    {
        $this->autoRender = false;

        $statusProcess      = isset($this->request->data['v_status_processamento']) ? $this->request->data['v_status_processamento'] : false;
        $pedido_operadora   = isset($this->request->data['v_pedido_operadora']) ? $this->request->data['v_pedido_operadora'] : false;
        $data_entrega       = isset($this->request->data['v_data_entrega']) ? $this->request->data['v_data_entrega'] : false;
        $motivo             = isset($this->request->data['v_motivo']) ? $this->request->data['v_motivo'] : false;

        $itemOrderId = isset($this->request->data['notOrderItemIds']) ? $this->request->data['notOrderItemIds'] : false;

        $de     = isset($this->request->data['curr_de']) ? $this->request->data['curr_de'] : false;
        $para   = isset($this->request->data['curr_para']) ? $this->request->data['curr_para'] : false;
        $num    = isset($this->request->data['curr_num']) ? $this->request->data['curr_num'] : false;
        $sup    = isset($this->request->data['curr_sup']) ? $this->request->data['curr_sup'] : false;
        $st     = isset($this->request->data['curr_st']) ? $this->request->data['curr_st'] : false;
        $c      = isset($this->request->data['curr_c']) ? $this->request->data['curr_c'] : false;
        $q      = isset($this->request->data['curr_q']) ? $this->request->data['curr_q'] : false;

        $condition = ['and' => ['Order.data_cancel' => '1901-01-01 00:00:00', 'OrderItem.id !=' => $itemOrderId], 'or' => []];
        
        $buscar = false;
        $de = null;
        $para = null;
        if (isset($de) and $de != '') {
            $buscar = true;

            $deRaw = $de;
            $dateObjectDe = DateTime::createFromFormat('d/m/Y', $deRaw);
            $de = $dateObjectDe->format('Y-m-d');
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $de]);
        }

        if (isset($para) and $para != '') {
            $buscar = true;

            $paraRaw = $para;
            $dateObjectPara = DateTime::createFromFormat('d/m/Y', $paraRaw);
            $para = $dateObjectPara->format('Y-m-d');
            $condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $para . ' 23:59:59']);
        }

        if (isset($sup) and $sup != 'Selecione') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Supplier.id' => $sup]);
        }

        if (isset($num) && $num != '') {
            $buscar = true;

            // Dividindo a entrada em uma matriz de números
            $selectedNumbers = preg_split("/[\s,]+/", $num);
            
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

        if (isset($st) and $st != '') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Order.status_id' => $st]);
        }

        if (isset($c) and $c != 'Selecione') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Customer.id' => $c]);
        }

        if (!empty($q)) {
            $buscar = true;

            $condition['or'] = array_merge($condition['or'], [
                'CustomerUser.name LIKE' => '%' . $q . '%',
                'CustomerUser.email LIKE' => '%' . $q . '%',
                'CustomerUser.cpf LIKE' => '%' . $q . '%',
                'Customer.nome_primario LIKE' => '%' . $q . '%',
                'Customer.documento LIKE' => '%' . $q . '%',
                'Order.id LIKE' => '%' . $q . '%',
                'Customer.id LIKE' => '%' . $q . '%',
                'Customer.codigo_associado LIKE' => "%" . $q . "%",

            ]);
        }

        $items = $this->OrderItem->find('all', [
            'fields' => ['OrderItem.id'],
            'conditions' => $condition,
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.id = CustomerUserItinerary.benefit_id', 'Benefit.data_cancel' => '1901-01-01',
                    ]
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Supplier.id = Benefit.supplier_id', 'Supplier.data_cancel' => '1901-01-01',
                    ]
                ],
                [
                    'table' => 'customers',
                    'alias' => 'Customer',
                    'type' => 'INNER',
                    'conditions' => [
                        'Customer.id = Order.customer_id', 'Customer.data_cancel' => '1901-01-01',
                    ],
                ],
                [
                    'table' => 'statuses',
                    'alias' => 'Status',
                    'type' => 'INNER',
                    'conditions' => [
                        'Status.id = Order.status_id',
                    ]
                ],
            ]
        ]);

        foreach ($items as $item) {
            $orderItem = $this->OrderItem->findById($item['OrderItem']['id']);

            $dados_log = [
                "old_value" => $orderItem['OrderItem']['status_processamento'] ? $orderItem['OrderItem']['status_processamento'] : ' ',
                "new_value" => $statusProcess,
                "route" => "orders/compras",
                "log_action" => "Alterou",
                "log_table" => "OrderItem",
                "primary_key" => $item['OrderItem']['id'],
                "parent_log" => 0,
                "user_type" => "ADMIN",
                "user_id" => CakeSession::read("Auth.User.id"),
                "message" => "O status_processamento do item foi alterado com sucesso",
                "log_date" => date("Y-m-d H:i:s"),
                "data_cancel" => "1901-01-01",
                "usuario_data_cancel" => 0,
                "ip" => $_SERVER["REMOTE_ADDR"]
            ];

            $this->Log->create();
            $this->Log->save($dados_log);

            $data = [
                'OrderItem' => [
                    'id' => $orderItem['OrderItem']['id'],
                    'status_processamento' => $statusProcess,
                    'pedido_operadora' => $pedido_operadora,
                    'data_entrega' => $data_entrega,
                    'updated_user_id' => CakeSession::read("Auth.User.id"),
                    'updated' => date('Y-m-d H:i:s'),
                ]
            ];

            if ($motivo) {
                $data['OrderItem']['motivo_processamento'] = $motivo;
            }

            $this->OrderItem->save($data);

            if ($statusProcess == 'CREDITO_INCONSISTENTE') {
                $orderBalanceData = [
                    'order_id' => $orderItem['Order']['id'],
                    'order_item_id' => $orderItem['OrderItem']['id'],
                    'customer_user_id' => $orderItem['CustomerUser']['id'],
                    'benefit_id' => $orderItem['CustomerUserItinerary']['benefit_id'],
                    'document' => $orderItem['CustomerUser']['cpf'],
                    'total' => $orderItem['OrderItem']['subtotal'],
                    'pedido_operadora' => $pedido_operadora,
                    'observacao' => $motivo,
                    'tipo' => 3,
                    'created' => date('Y-m-d H:i:s'),
                    'user_created_id' => CakeSession::read("Auth.User.id")
                ];

                $this->OrderBalance->create();
                $this->OrderBalance->save($orderBalanceData);
            }
        }

        echo json_encode(['success' => true]);
    }

    public function status_pedidos()
    {
        ini_set('memory_limit', '-1');

        $this->Permission->check(79, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Paginator->settings = [
            'Order' => [
                'contain' => ['Customer', 'CustomerCreator', 'EconomicGroup', 'Status', 'Creator', 'Income'],
                'fields' => [
                    'Order.*',
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
                'limit' => 50,
                'order' => ['Order.id' => 'desc'],
                'group' => ['Order.id'],
            ]
        ];

        if (!in_array(CakeSession::read("Auth.User.Group.name"), array('Administrador', 'Diretoria'))) {
            $condition = ["and" => ["Customer.id != " => 88357, "Order.status_id" => [85, 86], "Order.pedido_complementar" => 1 ], "or" => []];
        } else {
            $condition = ["and" => ["Order.status_id" => [85, 86], "Order.pedido_complementar" => 1 ], "or" => []];
        }

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

        if (isset($_GET['c']) and $_GET['c'] != 'Selecione') {
            $buscar = true;

            $condition['and'] = array_merge($condition['and'], ['Customer.id' => $_GET['c']]);
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

        if (!empty($_GET['tipo'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.is_partial' => $_GET['tipo']]);
        }

        $queryString = http_build_query($_GET);

        if (isset($_GET['exportar'])) {
            $nome = 'relatorio_status_pedidos_' . date('d_m_Y_H_i_s') . '.xlsx';

            $data = $this->Order->find('all', [
                'contain' => [
                    'Status',
                    'Customer',
                    'CustomerCreator',
                    'EconomicGroup',
                    'Income.data_pagamento',
                    'UpdatedGe',
                ],
                'conditions' => $condition,
            ]);

            foreach ($data as $k => $pedido) {
                $suppliersCount = $this->OrderItem->find('count', [
                    'conditions' => ['OrderItem.order_id' => $pedido['Order']['id']],
                    'joins' => [
                        [
                            'table' => 'benefits',
                            'alias' => 'Benefit',
                            'type' => 'INNER',
                            'conditions' => [
                                'Benefit.id = CustomerUserItinerary.benefit_id'
                            ]
                        ],
                        [
                            'table' => 'suppliers',
                            'alias' => 'Supplier',
                            'type' => 'INNER',
                            'conditions' => [
                                'Supplier.id = Benefit.supplier_id'
                            ]
                        ]
                    ],
                    'group' => ['Supplier.id'],
                    'fields' => ['Supplier.id']
                ]);

                $usersCount = $this->OrderItem->find('count', [
                    'conditions' => ['OrderItem.order_id' => $pedido['Order']['id']],
                    'group' => ['OrderItem.customer_user_id'],
                    'fields' => ['OrderItem.customer_user_id']
                ]);

                $order_balances_total = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $pedido['Order']['id'], "OrderBalance.tipo" => 1], 'fields' => 'SUM(OrderBalance.total) as total']);

                $data[$k]['Order']['suppliersCount'] = $suppliersCount;
                $data[$k]['Order']['usersCount'] = $usersCount;
                $data[$k]["Order"]["total_balances"] = $order_balances_total[0][0]['total'];
            }

            $this->ExcelGenerator->gerarExcelPedidoscustomer($nome, $data);

            $this->redirect("/files/excel/" . $nome);
        }

        $data = $this->Paginator->paginate('Order', $condition);

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

        $customers = $this->Customer->find('list', [
            'conditions' => ['Customer.status_id' => 3],
            'fields' => ['id', 'nome_primario'],
            'order' => ['nome_primario' => 'asc']
        ]);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 18, 'Status.id' => 104], 'order' => 'Status.name']);

        $action = 'Relatório Alteração Status Pedido';
        $breadcrumb = ['Relatórios' => '', 'Relatório Alteração Status Pedido' => ''];

        $this->set(compact('action', 'breadcrumb', 'status', 'data', 'totalOrders'));
    }

    public function alter_status_pedido()
    {
        $this->autoRender = false;

        $orderIds = $this->request->data['orderIds'];
        $status = $this->request->data['v_status_pedido'];

        foreach ($orderIds as $key => $value) {
            $order = $this->Order->findById($value);

            $dados_log = [
                "old_value" => $order['Order']['status_id'] ? $order['Order']['status_id'] : ' ',
                "new_value" => $status,
                "route" => "reports/status_pedidos",
                "log_action" => "Alterou",
                "log_table" => "Order",
                "primary_key" => $value,
                "parent_log" => 0,
                "user_type" => "ADMIN",
                "user_id" => CakeSession::read("Auth.User.id"),
                "message" => "O status_id do pedido foi alterado com sucesso",
                "log_date" => date("Y-m-d H:i:s"),
                "data_cancel" => "1901-01-01",
                "usuario_data_cancel" => 0,
                "ip" => $_SERVER["REMOTE_ADDR"]
            ];

            $this->Log->create();
            $this->Log->save($dados_log);

            $this->Order->save([
                'Order' => [
                    'id' => $order['Order']['id'],
                    'status_id' => $status,
                    'updated_user_id' => CakeSession::read("Auth.User.id"),
                    'updated' => date('Y-m-d H:i:s'),
                ]
            ]);
        }
        
        echo json_encode(['success' => true]);
    }
}
