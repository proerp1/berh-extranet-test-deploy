<?php 
App::uses('AuthComponent', 'Controller/Component');
class PlanoConta extends AppModel {
	public $name = 'PlanoConta';
	public $useTable = 'planocontas';
	public $primaryKey = 'id';
	var $virtualFields = array(
		'full_name' => 'CONCAT(PlanoConta.numero, " - ", PlanoConta.name)',
		'contabeis' => 'CONCAT(PlanoConta.referencia, " - ", PlanoConta.name)'
	);
	public $displayField = 'full_name';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('PlanoConta.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $validate = array(
		'numero' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'name' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'nivel' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'status_id' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		)
	);
}