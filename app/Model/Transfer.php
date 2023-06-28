<?php 
App::uses('AuthComponent', 'Controller/Component');
class Transfer extends AppModel {
	public $name = 'Transfer';

	public $belongsTo = array(
		'BankAccountOrigin' => array(
			'className' => 'BankAccount',
			'foreignKey' => 'bank_account_origin_id'
		),
		'BankAccountDest' => array(
			'className' => 'BankAccount',
			'foreignKey' => 'bank_account_dest_id'
		),
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 8)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Transfer.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['value'])) {
			$this->data[$this->alias]['value'] = $this->priceFormatBeforeSave($this->data[$this->alias]['value']);
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
			if (isset($val[$this->alias]['value'])) {
				$results[$key][$this->alias]['value_nao_formatado'] = $results[$key][$this->alias]['value'];
				$results[$key][$this->alias]['value'] = number_format($results[$key][$this->alias]['value'],2,',','.');
			}
		}

		return $results;
	}

	public $validate = array(
		'bank_account_origin_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'bank_account_dest_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'value' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);

}