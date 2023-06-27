<?php
class DashboardController extends AppController {
	public $helpers = array('Html', 'Form');
	public $components = array('Paginator', 'Permission', 'Email', 'Sms');
	public $uses = array("PlanCustomer", "Income", "RetornoCnab", "CustomerDiscountsProduct", 'Seller', 'Customer');

	public $paginate = ['PlanCustomer'	=> ['order' => ['Customer.created' => 'desc']],
											'Income'				=> ['order' => ['Income.created' => 'desc']]
										 ];

	public function beforeFilter() { 
		parent::beforeFilter(); 
	}

	public function index(){
		$this->Permission->check(4, "leitura")? "" : $this->redirect("/not_allowed");

		$ultimo_processamento = $this->RetornoCnab->find('first', ['conditions' => ['RetornoCnab.status_id' => 40], 'order' => ['RetornoCnab.id' => 'desc']]);

		$condition = ["and" => ["Customer.created BETWEEN '".date("Y-m"). "-01' AND '" .date("Y-m-t")."'", 'PlanCustomer.status_id' => 1], "or" => []];

		$where = ["and" => [], "or" => ["Income.data_pagamento" => date("Y-m-d")]];

		if (!empty($ultimo_processamento)) {
			$where = array_merge($where['or'], ["Income.data_pagamento" => $ultimo_processamento['RetornoCnab']['data_arquivo']]);
		}

		$contratos_vendidos = $this->Customer->find("all", [
					'conditions' => $condition,
					'order' => ['Customer.created' => 'asc'],
					'fields' => ['Customer.*', 'PlanCustomer.*', 'Plan.*', 'Seller.*', 'Statuses.*'],
					'group' => ['Customer.id'],
					'joins' => [
						[
							'table' => 'plan_customers',
							'alias' => 'PlanCustomer',
							'type' => 'Left',
							'conditions' => ['Customer.id = PlanCustomer.customer_id', 'PlanCustomer.status_id' => 1]
						],
						[
							'table' => 'statuses',
							'alias' => 'Statuses',
							'type' => 'Inner',
							'conditions' => ['Statuses.id = Customer.status_id']
						],
						[
							'table' => 'plans',
							'alias' => 'Plan',
							'type' => 'Left',
							'conditions' => ['Plan.id = PlanCustomer.plan_id']
						]
					]
				]);

		
		$titulos_pagos = $this->Income->find("all", ['conditions' => $where]);

		//retra os relacionametos para não haver peso extra na query
		$this->Income->recursive = -1;
		//contas a receber
		$receber_hoje = $this->Income->find('first', ['conditions' => ['Income.vencimento' => date('Y-m-d'), 'Income.status_id' => 15], 'fields' => ['sum(Income.valor_total) as receber_hoje']]);
		$receber_hoje = $receber_hoje[0]['receber_hoje'];

		//contas recebidas
		
		$recebidas_hoje = $this->Income->find('first', ['conditions' => ['Income.data_pagamento' => date('Y-m-d'), 'Income.data_pagamento' => date('Y-m-d', strtotime("-1 days")), 'Income.status_id' => 17], 'fields' => ['sum(Income.valor_pago) as recebidas_hoje']]);
		$recebidas = $recebidas_hoje[0]["recebidas_hoje"];

		$action = 'Dashboard';
		$this->set(compact("contratos_vendidos", "titulos_pagos", "receber_hoje", "recebidas", "action"));
	}
}