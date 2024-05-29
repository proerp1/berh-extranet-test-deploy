<?php 
App::uses('AuthComponent', 'Controller/Component');
class CustomerFile extends AppModel {
	public $name = 'CustomerFile';
	
	public $belongsTo = array(
		'Customer',
		'Layout',
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 21)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('CustomerFile.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $actsAs = array(
		'Upload.Upload' => array(
			'file'
		)
	);
}
