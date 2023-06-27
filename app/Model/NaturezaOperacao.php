<?php 
App::uses('AuthComponent', 'Controller/Component');
class NaturezaOperacao extends AppModel {
	public $name = 'NaturezaOperacao';
	public $useTable = 'natureza_operacao';
	public $displayField = 'nome';

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('NaturezaOperacao.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}
}