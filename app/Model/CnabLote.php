<?php 
App::uses('AuthComponent', 'Controller/Component');
class CnabLote extends AppModel {
	public $name = 'CnabLote';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 12)
		),
		'Bank'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('CnabLote.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}
}