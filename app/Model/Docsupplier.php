<?php 
App::uses('AuthComponent', 'Controller/Component');
class Docsupplier extends AppModel {
	public $name = 'Docsupplier';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		),
		'Supplier'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Docsupplier.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $actsAs = array(
		'Upload.Upload' => array(
			'file'
		)
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