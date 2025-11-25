<?php 
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');

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
		'Supplier',
		'UserUpdated' => array(
			'className' => 'User',
			'foreignKey' => 'user_updated_id'
		),
	);

  	public $hasMany = array(
    	'OutcomeOrder'
  	);

	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('Outcome.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) 
	{
		if (!empty($this->data['Outcome']['vencimento'])) {
			$this->data['Outcome']['vencimento'] = $this->dateFormatBeforeSave($this->data['Outcome']['vencimento']);
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
        if (empty($this->data[$this->alias]['id']) && empty($this->data[$this->alias]['created'])) {
            $this->data[$this->alias]['created'] = date('Y-m-d H:i:s');
        } elseif (!empty($this->data[$this->alias]['created'])) {
            $this->data[$this->alias]['created'] = $this->dateFormatBeforeSave($this->data[$this->alias]['created']);
        }

		$this->checkOrderStatus();

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
				$results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
				$results[$key]['Outcome']['created'] = date("d/m/Y", strtotime($val['Outcome']['created']));
			}
			if (isset($val[$this->alias]['updated'])) {
				$results[$key][$this->alias]['updated_nao'] = $val[$this->alias]['updated'];
				$results[$key][$this->alias]['updated_formatado'] = date("d/m/Y H:i:s", strtotime($val['Outcome']['updated']));
			}
			if (isset($val['Outcome']['data_pagamento'])) {
				$results[$key]['Outcome']['data_pagamento_nao_formatado'] = $val['Outcome']['data_pagamento'];
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

	private function checkOrderStatus() 
	{
		$outcome_id = !empty($this->data[$this->alias]['id'])
			? $this->data[$this->alias]['id']
			: $this->id;

		if (empty($outcome_id)) {
			return;
		}

		if (!isset($this->data[$this->alias]['status_id'])) {
			return;
		}

		$new_status_id = (int)$this->data[$this->alias]['status_id'];

		$old_status_id = (int)$this->field(
			'status_id',
			[$this->alias . '.id' => $outcome_id]
		);

		if ($old_status_id === $new_status_id) {
			return;
		}

		$this->registerStatusChangeLog($outcome_id, $old_status_id, $new_status_id);

		if ($new_status_id !== 13) {
			return;
		}

		if ($old_status_id === 13) {
			return;
		}

		$this->updateOrderItemsStatus($outcome_id);
	}

	private function updateOrderItemsStatus($outcome_id) 
	{
		$orderItem = ClassRegistry::init('OrderItem');

		$orderItem->updateAll(
			['OrderItem.status_processamento' => "'PAGAMENTO_REALIZADO'"],
			['OrderItem.outcome_id' => $outcome_id]
		);
	}

	private function registerStatusChangeLog($outcome_id, $old_status_id, $new_status_id)
	{
		$oldStatus = $this->Status->find('first', array(
			'conditions' => array('Status.id' => $old_status_id),
			'recursive'  => -1
		));

		$newStatus = $this->Status->find('first', array(
			'conditions' => array('Status.id' => $new_status_id),
			'recursive'  => -1
		));

		$oldName = !empty($oldStatus['Status']['name']) ? $oldStatus['Status']['name'] : 'N/A';
		$newName = !empty($newStatus['Status']['name']) ? $newStatus['Status']['name'] : 'N/A';

		$description = 'Mudança do status ' . $oldName . ' para ' . $newName;

		$userId = CakeSession::read('Auth.User.id');

		$OutcomeLog = ClassRegistry::init('OutcomeLog');

		$OutcomeLog->create();
		$OutcomeLog->save(array(
			'OutcomeLog' => array(
				'outcome_id'      => $outcome_id,
				'description'     => $description,
				'created'         => date('Y-m-d H:i:s'),
				'user_creator_id' => $userId,
			)
		));
	}
}