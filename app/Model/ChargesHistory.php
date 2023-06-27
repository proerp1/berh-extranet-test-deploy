<?php 
App::uses('AuthComponent', 'Controller/Component');
class ChargesHistory extends AppModel {
	public $name = 'ChargesHistory';

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_creator_id'
		),
		'Income'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('ChargesHistory.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data['ChargesHistory']['return_date'])) {
			$this->data['ChargesHistory']['return_date'] = $this->dateFormatBeforeSave($this->data['ChargesHistory']['return_date']);
		}
		
		if (!empty($this->data['ChargesHistory']['due_date'])) {
			$this->data['ChargesHistory']['due_date'] = $this->dateFormatBeforeSave($this->data['ChargesHistory']['due_date']);
		}

		if (!empty($this->data['ChargesHistory']['value'])) {
			$this->data['ChargesHistory']['value'] = $this->priceFormatBeforeSave($this->data['ChargesHistory']['value']);
		}

		if (!empty($this->data['ChargesHistory']['total_value'])) {
			$this->data['ChargesHistory']['total_value'] = $this->priceFormatBeforeSave($this->data['ChargesHistory']['total_value']);
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
			if (isset($val['ChargesHistory']['return_date'])) {
				$results[$key]['ChargesHistory']['return_date'] = date("d/m/Y", strtotime($val['ChargesHistory']['return_date']));
			}
			
			if (isset($val['ChargesHistory']['due_date'])) {
				$results[$key]['ChargesHistory']['due_date'] = date("d/m/Y", strtotime($val['ChargesHistory']['due_date']));
			}

			if (isset($val['ChargesHistory']['value'])) {
				$results[$key]['ChargesHistory']['value'] = number_format($results[$key]['ChargesHistory']['value'],2,',','.');
			}

			if (isset($val['ChargesHistory']['total_value'])) {
				$results[$key]['ChargesHistory']['total_value'] = number_format($results[$key]['ChargesHistory']['total_value'],2,',','.');
			}
		}

		return $results;
	}

	public $validate = array(
		'call_status' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O nome é obrigatório'
			)
		),
		'text' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O nome é obrigatório'
			)
		)
	);

}