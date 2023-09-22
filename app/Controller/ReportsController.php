<?php
App::import('Controller', 'Incomes');
class ReportsController extends AppController
{
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'ExcelConfiguration'];
	public $uses = ['Income', 'Customer', 'OrderItem', 'CostCenter', 'CustomerDepartment'];

	public $paginate = [
		'OrderItem'	=> [
			'limit' => 100, 
			'order' => ['OrderItem.id' => 'desc'],
			'fields' => [
				'Customer.nome_primario',
                'Customer.nome_secundario',
				'Customer.documento',
				'CustomerUser.name',
				'CustomerUser.cpf',
				'CustomerUser.tel',
				'CustomerUser.cel',
				'CustomerDepartment.name',
				'CustomerUserItinerary.unit_price',
				'CustomerUserItinerary.quantity',
				'OrderItem.*',
				'Benefit.code',
				'Supplier.code'
			],
			'joins' => [
				[
					'table' => 'orders',
					'alias' => 'Order',
					'type' => 'INNER',
					'conditions' => ['Order.id = OrderItem.order_id']
				],
				[
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'INNER',
					'conditions' => ['Customer.id = Order.customer_id']
				],
				[
					'table' => 'customer_users',
					'alias' => 'CustomerUser',
					'type' => 'INNER',
					'conditions' => ['CustomerUser.id = OrderItem.customer_user_id']
				],
				[
					'table' => 'customer_departments',
					'alias' => 'CustomerDepartment',
					'type' => 'LEFT',
					'conditions' => ['CustomerDepartment.id = CustomerUser.customer_departments_id']
				],
				[
					'table' => 'cost_center',
					'alias' => 'CostCenter',
					'type' => 'LEFT',
					'conditions' => ['CostCenter.id = CustomerUser.customer_departments_id']
				],
				[
					'table' => 'customer_user_itineraries',
					'alias' => 'CustomerUserItinerary',
					'type' => 'INNER',
					'conditions' => ['CustomerUserItinerary.id = OrderItem.customer_user_itinerary_id']
				],
				[
					'table' => 'benefits',
					'alias' => 'Benefit',
					'type' => 'INNER',
					'conditions' => ['Benefit.id = CustomerUserItinerary.benefit_id']
				],
				[
					'table' => 'suppliers',
					'alias' => 'Supplier',
					'type' => 'INNER',
					'conditions' => ['Supplier.id = Benefit.supplier_id']
				]
			],
			'recursive' => -1,
		]
	];

	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	public function index()
	{
		 $this->Permission->check(64, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => [], "or" => []];

		if(!isset($_GET['de']) && !isset($_GET['para'])){
			$dates = $this->getCurrentDates();
			$condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $dates['from']]);
			$condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $dates['to'].' 23:59:59']);

			$de = $dates['from'];
			$para = $dates['to'];
		}

		if (isset($_GET['de']) and $_GET['de'] != "") {
			$deRaw = $_GET['de'];
			$dateObjectDe = DateTime::createFromFormat('d/m/Y', $deRaw);
			$de = $dateObjectDe->format('Y-m-d');
			$condition['and'] = array_merge($condition['and'], ['OrderItem.created >=' => $de]);
		}

		if (isset($_GET['para']) and $_GET['para'] != "") {
			$paraRaw = $_GET['para'];
			$dateObjectPara = DateTime::createFromFormat('d/m/Y', $paraRaw);
			$para = $dateObjectPara->format('Y-m-d');
			$condition['and'] = array_merge($condition['and'], ['OrderItem.created <=' => $para.' 23:59:59']);
		}

		if (isset($_GET['d']) and $_GET['d'] != "Selecione") {
			$condition['and'] = array_merge($condition['and'], ['CustomerDepartment.id' => $_GET['d']]);
		}

		if (isset($_GET['cc']) and $_GET['cc'] != "Selecione") {
			$condition['and'] = array_merge($condition['and'], ['CostCenter.id' => $_GET['cc']]);
		}

        if (isset($_GET['c']) and $_GET['c'] != "Selecione") {
			$condition['and'] = array_merge($condition['and'], ['Customer.id' => $_GET['c']]);
		} else {
            $condition['and'] = array_merge($condition['and'], ['1 = 2']);
        }

		if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], [
				'CustomerUser.name LIKE' => "%".$_GET['q']."%", 
				'CustomerUser.email LIKE' => "%".$_GET['q']."%", 
				'CustomerUser.cpf LIKE' => "%".$_GET['q']."%",
                'Customer.nome_primario LIKE' => "%".$_GET['q']."%",
                'Customer.documento LIKE' => "%".$_GET['q']."%",
			]);
        }

		if (isset($_GET['excel'])) {
			$this->paginate['OrderItem'] = $this->ExcelConfiguration->getConfiguration('OrderItem');
			$this->Paginator->settings = $this->paginate;
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

			$this->Paginator->settings['OrderItem']['order'] = $order .' '.$direction;
		}

		$data = $this->Paginator->paginate('OrderItem', $condition);

        $customers = $this->Customer->find('list', ['fields' => ['id', 'nome_primario'], 'conditions' => ['Customer.status_id' => 3], 'recursive' => -1]);
        
		if (isset($_GET['excel'])) {
			$this->ExcelGenerator->gerarExcelItineraries('itinerarios', $data);

			$this->redirect('/private_files/baixar/excel/itinerarios_xlsx');
		}

		$de = date('d/m/Y', strtotime($de));
		$para = date('d/m/Y', strtotime($para));

		$action = 'Itinerários';
		$breadcrumb = ['Relatórios' => '', 'Itinerários' => ''];
		$this->set(compact('data', 'action', 'breadcrumb', 'costCenters', 'departments', 'de', 'para', 'customers'));
	}

    public function getDepAndCCByCustomer(){
        $this->autoRender = false;

        $customer_id = $_POST['customer_id'];

        $departments = $this->CustomerDepartment->find('all', ['fields' => ['id', 'name'], 'conditions' => ['CustomerDepartment.customer_id' => $customer_id], 'recursive' => -1]);
        $costCenters = $this->CostCenter->find('all', ['fields' => ['id', 'name'], 'conditions' => ['CostCenter.customer_id' => $customer_id], 'recursive' => -1]);

        echo json_encode(['departments' => $departments, 'costCenters' => $costCenters]);
    }

	private function getCurrentDates(){
		$currentDate = new DateTime();

		$firstDayOfMonth = new DateTime($currentDate->format('Y-m-01'));

		$to = $currentDate;

		$from = $firstDayOfMonth->format('Y-m-d');
		$to = $to->format('Y-m-d');

		return compact('from', 'to');
	}
}
