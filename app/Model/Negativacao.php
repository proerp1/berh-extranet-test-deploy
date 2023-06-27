<?php 
App::uses('AuthComponent', 'Controller/Component');
class Negativacao extends AppModel {
	public $name = 'Negativacao';
	public $useTable = 'negativacao';

	public $belongsTo = array(
		'Product',
		'Billing',
		'Customer'
	);

	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['valor_unitario_excel'])) {
			$this->data[$this->alias]['valor_unitario_excel'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_unitario_excel']);
		}

		if (!empty($this->data[$this->alias]['valor_total_excel'])) {
			$this->data[$this->alias]['valor_total_excel'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_total_excel']);
		}
		
		return true;
	}

	public function priceFormatBeforeSave($price) {
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

	public function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val['Negativacao']['valor_unitario'])) {
				$results[$key]['Negativacao']['valor_unitario_formatado'] = number_format($results[$key]['Negativacao']['valor_unitario'],2,',','.');
			}
			if (isset($val['Negativacao']['valor_total'])) {
				$results[$key]['Negativacao']['valor_total_formatado'] = number_format($results[$key]['Negativacao']['valor_total'],2,',','.');
			}
		}

		return $results;
	}

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Negativacao.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function find_negativacao_cliente($billing_id, $customer_id) {
		$result = $this->query("SELECT n.id, p.name, n.qtde_consumo, n.qtde_excedente, n.valor_unitario, n.valor_total, n.type
															FROM negativacao n
																INNER JOIN products p ON p.id = n.product_id
															WHERE n.data_cancel = '1901-01-01' AND p.status_id = 1 AND n.customer_id = ".$customer_id." AND n.billing_id = ".$billing_id."");

		return $result;
	}
}