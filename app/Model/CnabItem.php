<?php 
App::uses('AuthComponent', 'Controller/Component');
class CnabItem extends AppModel {
	public $name = 'CnabItem';

	public $belongsTo = array(
		'CnabLote',
		'Income',
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 13)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('CnabItem.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}
}