<?php
class CustoSerasaController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission'];
	public $uses = ['Negativacao', 'Billing'];

	public function beforeFilter() { 
		parent::beforeFilter(); 
	}

	public function cliente() {
		$this->Permission->check(25, "leitura") ? "" : $this->redirect("/not_allowed");

		$condition = ["and" => [], "or" => []];

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Negativacao.billing_id' => $_GET['t']]);
		}

		if(!empty($_GET["q"])){
			$condition['or'] = array_merge($condition['or'], ['Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%"]);
		}

		$data = [];
		if (!empty($condition['and'])) {
			$data = $this->Negativacao->find('all', ['conditions' => $condition, 'group' => ['Customer.id'], 'fields' => ['Customer.nome_secundario', 'round(SUM(Negativacao.valor_total_excel), 2) as total'], 'order' => ['Customer.nome_secundario' => 'asc']]);
		} else {
			$this->Flash->set(__('Você deve filtrar por um faturamento.'), 'default', array('class' => "alert alert-warning"));
		}

		$faturamentos = $this->Billing->find('all', ['conditions' => ['Billing.status_id' => 1]]);
		
		$action = 'Custo Serasa por cliente';
		$this->set(compact('data', 'faturamentos', 'action'));
	}

	public function produto() {
		$this->Permission->check(26, "leitura") ? "" : $this->redirect("/not_allowed");

		$condition = ["and" => [], "or" => []];

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Negativacao.billing_id' => $_GET['t']]);
		}

		if(!empty($_GET["q"])){
			$condition['or'] = array_merge($condition['or'], ['Product.name LIKE' => "%".$_GET['q']."%"]);
		}

		$data = [];
		if (!empty($condition['and'])) {
			$data = $this->Negativacao->find('all', ['conditions' => $condition, 'group' => ['Product.id'], 'fields' => ['Product.name', 'round(SUM(Negativacao.valor_total_excel), 2) as total'], 'order' => ['Product.name' => 'asc']]);
		} else {
			$this->Flash->set(__('Você deve filtrar por um faturamento.'), 'default', array('class' => "alert alert-warning"));
		}

		$faturamentos = $this->Billing->find('all', ['conditions' => ['Billing.status_id' => 1]]);

		$action = 'Custo Serasa por produto';
		$this->set(compact('data', 'faturamentos', 'action'));
	}
}