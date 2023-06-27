<?php 
App::uses('AuthComponent', 'Controller/Component');
class Document extends AppModel {
	public $name = 'Document';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		),
		'Customer'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Document.data_cancel' => '1901-01-01 00:00:00');
		
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
				'rule' => array('notEmpty'),
				'message' => 'O nome é obrigatório'
			)
		),
		'status_id' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O nome é obrigatório'
			)
		)
	);

}