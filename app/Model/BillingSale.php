<?php 
App::uses('AuthComponent', 'Controller/Component');
class BillingSale extends AppModel {
	public $name = 'BillingSale';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_creator_id'
		),
		'Billing'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('BillingSale.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['mes_pagamento'])) {
			$this->data[$this->alias]['mes_pagamento'] = $this->date_converter('01/'.$this->data[$this->alias]['mes_pagamento']);
		}

		return true;
	}

	public function date_converter($_date = null) {
    $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
    if ($_date != null && preg_match($format, $_date, $partes)) {
      return $partes[3].'-'.$partes[2].'-'.$partes[1];
    }
    
    return false;
  }
}