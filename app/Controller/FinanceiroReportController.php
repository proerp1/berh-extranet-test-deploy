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

        $totalReceived = $this->Order->find('all', [
            'conditions' => [
                'Order.status_id' => 87,
                'Order.order_period_from >=' => date('Y-m-01'),
                'Order.order_period_to <=' => date('Y-m-t'),
            ],
            'fields' => ['sum(Order.total) as total'],
        ]);
        $totalReceivedRaw = $totalReceived[0][0]['total'];
        $totalReceived = number_format($totalReceivedRaw, 2, ',', '.');

        $totalDiscount = $this->OrderBalance->find('first', [
            'contain' => ['Order'],
            'conditions' => [
                'Order.status_id' => 87,
                'Order.order_period_from >=' => date('Y-m-01'),
                'Order.order_period_to <=' => date('Y-m-t'),
                'OrderBalance.tipo' => 1
            ],
            'fields' => ['sum(OrderBalance.total) as total'],
        ]);
        $totalDiscountRaw = $totalDiscount[0]['total'];
        $totalDiscount = number_format($totalDiscountRaw, 2, ',', '.');

        $this->set(compact('breadcrumb', 'action', 'totalReceived', 'totalDiscount', 'totalReceivedRaw', 'totalDiscountRaw'));
    }

    public function getRadarDash()
    {
        $this->autoRender = false;

        $ordersByDepartment = $this->OrderItem->find('all', [
            'fields' => ['count(DISTINCT CustomerUser.id) as total', 'CustomerDepartment.name'],
            'joins' => [
                [
                    'table' => 'customer_departments',
                    'alias' => 'CustomerDepartment',
                    'type' => 'INNER',
                    'conditions' => [
                        'CustomerUser.customer_departments_id = CustomerDepartment.id',
                    ],
                ],
            ],
            'conditions' => [
                'Order.order_period_from >=' => date('Y-m-01'),
                'Order.order_period_to <=' => date('Y-m-t'),
                'Order.status_id' => 87,
            ],
            'group' => ['CustomerDepartment.name'],
        ]);

        $ordersByCC = $this->OrderItem->find('all', [
            'fields' => ['count(DISTINCT CustomerUser.id) as total', 'CostCenter.name'],
            'joins' => [
                [
                    'table' => 'cost_center',
                    'alias' => 'CostCenter',
                    'type' => 'INNER',
                    'conditions' => [
                        'CustomerUser.customer_cost_center_id = CostCenter.id',
                    ],
                ],
            ],
            'conditions' => [
                'Order.order_period_from >=' => date('Y-m-01'),
                'Order.order_period_to <=' => date('Y-m-t'),
                'Order.status_id' => 87,
            ],
            'group' => ['CostCenter.name'],
        ]);

        $departmentHeader = [];
        $departmentData = [];
        foreach ($ordersByDepartment as $value) {
            $departmentHeader[] = $value['CustomerDepartment']['name'];
            $departmentData[] = $value[0]['total'];
        }

        $ccHeader = [];
        $ccData = [];
        foreach ($ordersByCC as $value) {
            $ccHeader[] = $value['CostCenter']['name'];
            $ccData[] = $value[0]['total'];
        }

        $result = [
            'department' => [
                'header' => $departmentHeader,
                'data' => $departmentData,
            ],
            'costCenter' => [
                'header' => $ccHeader,
                'data' => $ccData,
            ],
        ];

        echo json_encode($result);
    }

    public function getRankingOperadoras()
    {
        $this->autoRender = false;

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
                'Order.order_period_from >=' => date('Y-m-01'),
                'Order.order_period_to <=' => date('Y-m-t'),
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
                'Order.order_period_from >=' => date('Y-01-01'),
                'Order.order_period_to <=' => date('Y-12-31'),
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
