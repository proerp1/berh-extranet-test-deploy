<?php 
class Docoutcome extends AppModel {
	public $name = 'Docoutcome';

	public $belongsTo = array(

		'Outcome'
	);

	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('Docoutcome.data_cancel' => '1901-01-01 00:00:00');
		return $queryData;
	}

	public function getDocuments($conditions = [], $limit = 50) {
		$this->unbindModel(['belongsTo' => ['Outcome']]); // Unbind se não for necessário

		$query = $this->find('all', [
			'conditions' => $conditions,
			'limit' => $limit,
			'joins' => [
				[
					'table' => 'outcomes',
					'alias' => 'Outcome',
					'type' => 'LEFT',
					'conditions' => ['Docoutcome.outcome_id = Outcome.id']
				],
				[
					'table' => 'suppliers',
					'alias' => 'Supplier',
					'type' => 'LEFT',
					'conditions' => ['Outcome.supplier_id = Supplier.id']
				],
				[
					'table' => 'statuses',
					'alias' => 'Status',
					'type' => 'LEFT',
					'conditions' => ['Docoutcome.status_id = Status.id']
				],
				[
					'table' => 'statuses',
					'alias' => 'OutcomeStatus',
					'type' => 'LEFT',
					'conditions' => ['Outcome.status_id = OutcomeStatus.id']
				]
			],
			'fields' => [
				'Docoutcome.*', 
				'Outcome.*', 
				'Supplier.nome_fantasia', 
				'Status.*', 
				'OutcomeStatus.*'
			],
			'order' => [
				'Outcome.id' => 'asc',
				'Docoutcome.created' => 'asc'
			]
		]);

		return $query;
	}

	public $actsAs = array(
		'Upload.Upload' => array('file')
	);

	public $validate = array(
		'name' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'O nome é obrigatório'
			)
		),
		'status_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'O nome é obrigatório'
			)
		)
	);
}
