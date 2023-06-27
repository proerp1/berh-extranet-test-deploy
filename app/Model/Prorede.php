<?php 
App::uses('AuthComponent', 'Controller/Component');
class Prorede extends AppModel {
	public $name = 'Prorede';
	public $useTable = 'prorede';

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Prorede.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

}