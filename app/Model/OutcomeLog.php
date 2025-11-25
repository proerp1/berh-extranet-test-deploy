<?php 
App::uses('AuthComponent', 'Controller/Component');
class OutcomeLog extends AppModel {
	public $name = 'OutcomeLog';

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_creator_id'
		),
		'Outcome'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('OutcomeLog.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}
}