<?php

class FinanceiroReportController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['OrderBalance', 'Order', 'OrderItem'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $breadcrumb = ['Dashboard' => '/'];
        $action = 'Principal';

        $get_de = isset($_GET['de']) ? $_GET['de'] : date('01/m/Y');
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : date('t/m/Y');
    
        $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
        $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

        $totalReceived = $this->Order->find('all', [
            'conditions' => [
                'Order.status_id' => 87,
                'Order.order_period_from >= ?' => [$de],
                'Order.order_period_to <= ?' => [$ate],
            ],
            'fields' => ['sum(Order.total) as total'],
        ]);
        $totalReceivedRaw = $totalReceived[0][0]['total'];
        $totalReceived = number_format($totalReceivedRaw, 2, ',', '.');

        $totalDiscount = $this->OrderBalance->find('first', [
            'contain' => ['Order'],
            'conditions' => [
                'Order.status_id' => 87,
                'Order.order_period_from >= ?' => [$de],
                'Order.order_period_to <= ?' => [$ate],
                'OrderBalance.tipo' => 1
            ],
            'fields' => ['sum(OrderBalance.total) as total'],
        ]);
        $totalDiscountRaw = $totalDiscount[0]['total'];
        $totalDiscount = number_format($totalDiscountRaw, 2, ',', '.');

        $this->set(compact('breadcrumb', 'action', 'totalReceived', 'totalDiscount', 'totalReceivedRaw', 'totalDiscountRaw'));
    }

    public function getRankingOperadoras()
    {
        $this->autoRender = false;

        $get_de = isset($_GET['de']) ? $_GET['de'] : date('01/m/Y');
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : date('t/m/Y');
    
        $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
        $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

        $rankingOperadoras = $this->OrderItem->find('all', [
            'fields' => ['sum(Order.total) as total', 'Supplier.nome_fantasia'],
            'joins' => [
                [
                    'table' => 'benefits',
                    'alias' => 'Benefit',
                    'type' => 'INNER',
                    'conditions' => [
                        'CustomerUserItinerary.benefit_id = Benefit.id',
                    ],
                ],
                [
                    'table' => 'suppliers',
                    'alias' => 'Supplier',
                    'type' => 'INNER',
                    'conditions' => [
                        'Benefit.supplier_id = Supplier.id',
                    ],
                ],
            ],
            'conditions' => [
                'Order.order_period_from >= ?' => [$de],
                'Order.order_period_to <= ?' => [$ate],
                'Order.status_id' => 87,
            ],
            'group' => ['Supplier.nome_fantasia'],
            'limit' => 10,
            'order' => ['total' => 'DESC'],
        ]);

        $header = [];
        $data = [];
        foreach ($rankingOperadoras as $value) {
            $header[] = $value['Supplier']['nome_fantasia'];
            $data[] = (float) $value[0]['total'];
        }

        $result = [
            'header' => $header,
            'data' => $data,
        ];

        echo json_encode($result);
    }

    public function getEvolucaoPedidos()
    {
        $this->autoRender = false;

        $get_de = isset($_GET['de']) ? $_GET['de'] : date('01/m/Y');
        $get_ate = isset($_GET['ate']) ? $_GET['ate'] : date('t/m/Y');
    
        $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
        $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

        $this->Order->unbindModel([
            'hasMany' => ['OrderItem'],
        ]);

        $evolucaoPedidos = $this->Order->find('all', [
            'fields' => [
                'sum(Order.total) as total',
                '(select sum(total) from order_balances b where b.order_id = Order.id and b.tipo = 1 and b.data_cancel = "1901-01-01 00:00:00") as economia',
                "DATE_FORMAT(Order.order_period_from, '%m/%Y') as mes",
            ],
            'conditions' => [
                'Order.order_period_from >= ?' => [$de],
                'Order.order_period_to <= ?' => [$ate],
                'Order.status_id' => 87,
            ],
            'group' => ["DATE_FORMAT(Order.order_period_from, '%m/%Y')"],
            'order' => ["DATE_FORMAT(Order.order_period_from, '%m/%Y')"],
        ]);

        $data = [];
        foreach ($evolucaoPedidos as $value) {
            $data[] = [
                'mesAno' => $value[0]['mes'],
                'totalPedido' => (float) $value[0]['total'],
                'totalEconomia' => (float) $value[0]['economia'],
            ];
        }

        $result = [
            'data' => $data,
        ];

        echo json_encode($result);
    }
}
