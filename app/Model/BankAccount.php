<?php 
App::uses('AuthComponent', 'Controller/Component');
class BankAccount extends AppModel {
	public $name = 'BankAccount';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('BankAccount.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['start_date'])) {
      $this->data[$this->alias]['start_date'] = $this->dateFormatBeforeSave($this->data[$this->alias]['start_date']);
    }

		if (!empty($this->data[$this->alias]['initial_balance'])) {
			$this->data[$this->alias]['initial_balance'] = $this->priceFormatBeforeSave($this->data[$this->alias]['initial_balance']);
		}

		if (!empty($this->data[$this->alias]['limit'])) {
			$this->data[$this->alias]['limit'] = $this->priceFormatBeforeSave($this->data[$this->alias]['limit']);
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
			if (isset($val[$this->alias]['initial_balance'])) {
				$results[$key][$this->alias]['initial_balance_not_formated'] = $results[$key][$this->alias]['initial_balance'];
				$results[$key][$this->alias]['initial_balance'] = number_format($results[$key][$this->alias]['initial_balance'],2,',','.');
			}

			if (isset($val[$this->alias]['limit'])) {
				$results[$key][$this->alias]['limit_not_formated'] = $results[$key][$this->alias]['limit'];
				$results[$key][$this->alias]['limit'] = number_format($results[$key][$this->alias]['limit'],2,',','.');
			}

			if (isset($val[$this->alias]['start_date'])) {
        $results[$key][$this->alias]['start_date_nao_formatado'] = $val[$this->alias]['start_date'];
        $results[$key][$this->alias]['start_date'] = date("d/m/Y", strtotime($val[$this->alias]['start_date']));
      }
		}

		return $results;
	}

	public $validate = array(
		'name' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'agency' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'account_number' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'initial_balance' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'limit' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);

}