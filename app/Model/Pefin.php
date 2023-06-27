<?php 
App::uses('AuthComponent', 'Controller/Component');
class Pefin extends AppModel {
	public $name = 'Pefin';
	public $useTable = 'pefin';

	public $belongsTo = array(
		'Product',
		'Billing',
		'Customer'
	);

	public function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val['Pefin']['valor_unitario'])) {
				$results[$key]['Pefin']['valor_unitario_formatado'] = number_format($results[$key]['Pefin']['valor_unitario'],2,',','.');
			}
			if (isset($val['Pefin']['valor_total'])) {
				$results[$key]['Pefin']['valor_total_formatado'] = number_format($results[$key]['Pefin']['valor_total'],2,',','.');
			}
		}

		return $results;
	}

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Pefin.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function find_pefin_cliente($billing_id, $customer_id) {
		$result = $this->query("SELECT n.id, p.name, n.qtde_excedente, n.qtde_realizado, n.valor_unitario, n.valor_total
															FROM pefin n
																INNER JOIN products p ON p.id = n.product_id
															WHERE n.data_cancel = '1901-01-01' AND p.status_id = 1 AND n.customer_id = ".$customer_id." AND n.billing_id = ".$billing_id."");

		return $result;
	}
}