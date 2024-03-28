<?php 
App::uses('AuthComponent', 'Controller/Component');
class Outcome extends AppModel {
	public $name = 'Outcome';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 4)
		),
		'BankAccount',
		'Expense',
		'CostCenter',
		'Supplier'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Outcome.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data['Outcome']['vencimento'])) {
			$this->data['Outcome']['vencimento'] = $this->dateFormatBeforeSave($this->data['Outcome']['vencimento']);
		}

		if (!empty($this->data[$this->alias]['created'])) {
            $created_date = date_create_from_format('d/m/Y', $this->data[$this->alias]['created']); 
        }

		if (!empty($this->data['Outcome']['valor_bruto'])) {
			$this->data['Outcome']['valor_bruto'] = $this->priceFormatBeforeSave($this->data['Outcome']['valor_bruto']);
		}

		if (!empty($this->data['Outcome']['valor_multa'])) {
			$this->data['Outcome']['valor_multa'] = $this->priceFormatBeforeSave($this->data['Outcome']['valor_multa']);
		}

		if (!empty($this->data['Outcome']['valor_desconto'])) {
			$this->data['Outcome']['valor_desconto'] = $this->priceFormatBeforeSave($this->data['Outcome']['valor_desconto']);
		}

		if (!empty($this->data['Outcome']['valor_total'])) {
			$this->data['Outcome']['valor_total'] = $this->priceFormatBeforeSave($this->data['Outcome']['valor_total']);
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
			if (isset($val['Outcome']['vencimento'])) {
	        	$results[$key]['Outcome']['vencimento'] = date("d/m/Y", strtotime($val['Outcome']['vencimento']));
	      	}
			  if (isset($val['Outcome']['created'])) {
	        	$results[$key]['Outcome']['created'] = date("d/m/Y", strtotime($val['Outcome']['created']));
	      	}
	      	if (isset($val['Outcome']['data_pagamento'])) {
	        	$results[$key]['Outcome']['data_pagamento'] = date("d/m/Y", strtotime($val['Outcome']['data_pagamento']));
	      	}
			if (isset($val['Outcome']['valor_bruto'])) {
				$results[$key]['Outcome']['valor_bruto'] = number_format($results[$key]['Outcome']['valor_bruto'],2,',','.');
			}
			if (isset($val['Outcome']['valor_multa'])) {
				$results[$key]['Outcome']['valor_multa'] = number_format($results[$key]['Outcome']['valor_multa'],2,',','.');
			}

			if (isset($val['Outcome']['valor_desconto'])) {
				$results[$key]['Outcome']['valor_desconto'] = number_format($results[$key]['Outcome']['valor_desconto'],2,',','.');
			}
			if (isset($val['Outcome']['valor_total'])) {
				$results[$key]['Outcome']['valor_total_not_formated'] = $results[$key]['Outcome']['valor_total'];
				$results[$key]['Outcome']['valor_total'] = number_format($results[$key]['Outcome']['valor_total'],2,',','.');
				
                
			}
			if (isset($val['Outcome']['valor_pago'])) {
				$results[$key]['Outcome']['valor_pago_not_formated'] = $results[$key]['Outcome']['valor_pago'];
				$results[$key]['Outcome']['valor_pago'] = number_format($results[$key]['Outcome']['valor_pago'],2,',','.');
				
                
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
		'doc_num' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'supplier_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'valor_bruto' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'valor_total' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'bank_account_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'status_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'cost_center_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'vencimento' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'expense_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);

}