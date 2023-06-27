<?php 
App::uses('AuthComponent', 'Controller/Component');
class CustomerPefin extends AppModel {
	public $name = 'CustomerPefin';
	
	public $belongsTo = array(
		'Customer',
		'NaturezaOperacao',
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 10)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('CustomerPefin.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['data_compra'])) {
			$this->data[$this->alias]['data_compra'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_compra']);
		}
		
		if (!empty($this->data[$this->alias]['venc_divida'])) {
			$this->data[$this->alias]['venc_divida'] = $this->dateFormatBeforeSave($this->data[$this->alias]['venc_divida']);
		}
		
		if (!empty($this->data[$this->alias]['valor'])) {
			$this->data[$this->alias]['valor'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor']);
		}

		return true;
	}

	public function priceFormatBeforeSave($price) {
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

	public function dateFormatBeforeSave($dateString) {
		return date('Y-m-d', strtotime($this->date_converter($dateString)));
	}

	public function date_converter($_date = null) {
		$format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
		if ($_date != null && preg_match($format, $_date, $partes)) {
			return $partes[3].'-'.$partes[2].'-'.$partes[1];
		}
		
		return false;
	}

	public function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val[$this->alias]['data_compra'])) {
				$results[$key][$this->alias]['data_compra_nao_formatado'] = $val[$this->alias]['data_compra'];
				$results[$key][$this->alias]['data_compra'] = date("d/m/Y", strtotime($val[$this->alias]['data_compra']));
			}
			
			if (isset($val[$this->alias]['venc_divida'])) {
				$results[$key][$this->alias]['venc_divida_nao_formatado'] = $val[$this->alias]['venc_divida'];
				$results[$key][$this->alias]['venc_divida'] = date("d/m/Y", strtotime($val[$this->alias]['venc_divida']));
			}
			
			if (isset($val[$this->alias]['valor'])) {
				$results[$key][$this->alias]['valor_nao_formatado'] = $val[$this->alias]['valor'];
				$results[$key][$this->alias]['valor'] = number_format($val[$this->alias]['valor'],2,",",".");
			}
		}

		return $results;
	}

	public $validate = array(
		'natureza_operacao_id' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'data_compra' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório',
				'last' => false
			),
			'date_format' => array(
				'rule' => array('date', 'dmy'),
				'message' => 'Digite uma data no formato DD/MM/YYYY.'
			)
		),
		'venc_divida' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			),
			'date_format' => array(
				'rule' => array('date', 'dmy'),
				'message' => 'Digite uma data no formato DD/MM/YYYY.'
			)
		),
		'valor' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório',
				'last' => false
			),
			'minValue' => array(
				'rule' => array('minValue', 15),
				'message' => 'O valor não pode ser menor que R$ 15,00'
			)
		)
	);

	public function minValue($check, $limit) {
		$valueFormatado = str_replace('.', '', $check);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		if ($valueFormatado['valor'] < $limit) {
			return false;
		}	else {
			return true;
		}
	}
}


