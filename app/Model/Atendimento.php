<?php 
App::uses('AuthComponent', 'Controller/Component');
class Atendimento extends AppModel {
	public $name = 'Atendimento';
	public $useTable = 'atendimento';

	public $belongsTo = array(
		'Customer',
		'Department',
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 9)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Atendimento.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val['Atendimento']['data_atendimento'])) {
				$results[$key]['Atendimento']['data_atendimento'] = date("d/m/Y H:i", strtotime($val['Atendimento']['data_atendimento']));
			}
		}

		return $results;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data['Atendimento']['data_atendimento'])) {
			$this->data['Atendimento']['data_atendimento'] = $this->dateTimeFormatBeforeSave($this->data['Atendimento']['data_atendimento']);
		}

		return true;
	}

	public function dateTimeFormatBeforeSave($dateString) {
		$date = explode(' ', $dateString);

		$date[0] = date('Y-m-d', strtotime(str_replace('/', '-', $date[0])));

		return $date[0].' '.$date[1];
	}

	public $validate = array(
		'subject' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'text' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'department_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'customer_id' => array(
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
		'message' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);

}