<?php 

class Bank extends AppModel {
	public $name = 'Bank';

	public function beforeFind($queryData) {

		$queryData['conditions'][] = ['Bank.data_cancel' => '1901-01-01 00:00:00'];
		
		return $queryData;
	}
}