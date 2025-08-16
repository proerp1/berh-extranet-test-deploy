<?php 
App::uses('AuthComponent', 'Controller/Component');
class OutcomeOrder extends AppModel {
	public $belongsTo = array(
		'Outcome'
	);

	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('OutcomeOrder.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}
}